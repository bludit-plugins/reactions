<?php
declare(strict_types=1);
/*
 |  Reactions   Let your users react to your content.
 |  @file       ./system/reactions.php
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.1 [1.0.0] - Stable
 |
 |  @website    https://github.com/pytesNET/reactions
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */
    defined("BLUDIT") or die("Go directly to Jail. Do not pass Go. Do not collect 200 Cookies!");


    // Main Reactions Class
    class Reactions extends dbJSON {
        /*
         |  CONSTRUCTOR
         |  @since  1.0.0
         */
        public function __construct() {
            global $reactions_plugin;
            $mode = $reactions_plugin->getValue("widget_mode");

            // Select Database
            parent::__construct(REACTIONS_WS . "votes-{$mode}.php");
            if(!file_exists(REACTIONS_WS . "votes-{$mode}.php")) {
                $this->db = [ ];
                $this->save();

                // Reset Session
                if(!Session::started()){
                    Session::start();
                }
                Session::set("my-reactions", []);
            }
        }

        /*
         |  HANDLE :: VOTABLE
         |  @since  1.0.0
         |
         |  @param  object  The page instance object.
         |
         |  @return bool    TRUE if the page can be voted, FALSE if not.
         */
        public function votable(Page $page): bool {
            global $reactions_plugin;

            // Check Type
            if(!in_array($page->type(), ["published", "static", "sticky"])) {
                return false;
            }
            if(!$reactions_plugin->getValue("show_on_" . $page->type())) {
                return false;
            }

            // Check Comments
            if(!$page->allowComments() && $reactions_plugin->getValue("hide_on_comments")) {
                return false;
            }
            return true;
        }

        /*
         |  HANDLE :: SECURITY CHECKs
         |  @since  1.0.0
         |
         |  @param  object  The page instance object.
         |
         |  @return bool    TRUE if the security checks has been solved, FALSE if not.
         */
        protected function protection(Page $page): bool {
            global $reactions_plugin;

            // Secure :: Check HoneyPot
            if($reactions_plugin->getValue("secure_honeypot")) {
                if(!empty($_POST["reactions-email"] ?? "")) {
                    return false;
                }
            }

            // Secure :: Check Session Cookie
            if($reactions_plugin->getValue("secure_cookies")) {
                if(Cookie::get("BLUDIT-KEY") === false) {
                    return false;
                }
                if(Session::get("REACTIONS-KEY") !== md5(Cookie::get("BLUDIT-KEY"))) {
                    return false;
                }
            }

            // Secure :: Filter
            if($reactions_plugin->getValue("secure_filter")) {
                if(stripos($_SERVER["HTTP_REFERER"] ?? "", $page->permalink()) === false) {
                    return false;
                }
                foreach(["bot", "crawl", "spider", "partner", "agent", "archiver"] AS $check) {
                    if(stripos($_SERVER["HTTP_USER_AGENT"], $check) !== false) {
                        return false;
                    }
                }
            }

            // Everything is Fine
            return true;
        }

        /*
         |  HANDLE :: VOTE
         |  @since  1.0.0
         |
         |  @param  multi   The page slug as STRING or the Page object.
         |  @param  int     The vote, which the user has made.
         |
         |  @return bool    TRUE if the vote has been counted, FALSE if not.
         */
        public function vote(string $slug, int $vote): bool {
            global $pages;
            global $reactions_plugin;

            // Validate Page
            if(is_string($slug) && $pages->exists($slug)) {
                $page = new Page($slug);
            } else if(is_a($slug, Page) && !empty($slug->key())) {
                $page = $slug;
            } else {
                return false;
            }

            // Validate Votability
            if($vote < 0 || $vote > 4 || !$this->votable($page)) {
                return false;
            }

            // Protection
            if(!$this->protection($page)) {
                return false;
            }

            // Validate Vote
            if(($old = $this->voted($slug)) === $vote) {
                return true;
            }
            if($old !== null) {
                $this->unvote($page);
            }

            // [PLUS] Cookie or Session Storage
            if(REACTIONS_PLUS && method_exists($this, "store")) {
                $this->store($page->key(), $vote);
            }

            // [PLUS] Log Votes
            if(REACTIONS_PLUS && method_exists($this, "log")) {
                $this->log($page->key(), $vote, $old);
            }

            // Add to Workspace
            if(!array_key_exists($page->key(), $this->db)) {
                $this->db[$page->key()] = "0,0,0,0,0";
            }
            $values = explode(",", $this->db[$page->key()]);
            $values[$vote]++;
            $this->db[$page->key()] = implode(",", $values);
            $this->save();

            // Add to Session
            $values = Session::get("my-reactions");
            if(!$values || !is_array($values)) {
                $values = [];
            }
            $values[$page->key()] = $vote;
            Session::set("my-reactions", $values);
            return true;
        }

        /*
         |  HANDLE :: UNVOTE
         |  @since  1.0.0
         |
         |  @param  multi   The page slug as STRING or the Page object.
         |
         |  @return bool    TRUE if the vote has been un-counted, FALSE if not.
         */
        public function unvote(/* string | Page */ $slug): bool {
            global $pages;

            // Validate Page
            if(is_string($slug) && $pages->exists($slug)) {
                $page = new Page($slug);
            } else if(is_a($slug, "Page") && !empty($slug->key())) {
                $page = $slug;
            } else {
                return false;
            }

            // Check Vote
            $values = Session::get("my-reactions");
            if(!$values || (is_array($values) && !array_key_exists($page->key(), $values))) {
                return false;
            }
            $vote = $values[$page->key()];

            // [PLUS] Unvote Cookie or Session
            if(REACTIONS_PLUS && method_exists($this, "unstore")) {
                $this->unstore($page->key());
            }

            // Unvote Workspace
            if(array_key_exists($page->key(), $this->db)) {
                $values = explode(",", $this->db[$page->key()]);
                $values[$vote]--;
                $this->db[$page->key()] = implode(",", $values);
                $this->save();
            }

            // Unvote Session
            unset($values[$page->key()]);
            return true;
        }

        /*
         |  HANDLE :: HAS VOTED
         |  @since  1.0.0
         |
         |  @param  string  The page slug.
         |
         |  @param  multi   The stored vote as integer or NULL if no vote has been made yet.
         */
        public function voted(string $slug): ?int {
            $values = Session::get("my-reactions");
            if(!$values || !is_array($values)) {
                $values = [];
            }
            if(array_key_exists($slug, $values)) {
                return $values[$slug];
            }
            return null;
        }

        /*
         |  HANDLE :: GET VOTES
         |  @since  1.0.0
         |
         |  @param  string  The slug of the page.
         |  @param  int     Returns the 5-Step rating in different formats.
         |                      1   Like        Calculated from the last 2 + 1/2 of the third
         |                      2   Dislike     Calculated from the first 2 + 1/2 of the third
         |                      3   Stars       Calculates the first, third and firth one.
         |                      5   Complete    All
         */
        public function votes(string $slug, int $steps = 5): array {
            if(array_key_exists($slug, $this->db)) {
                $values = explode(",", $this->db[$slug]);

                $return = [];
                switch($steps) {
                    case 2: //@pass
                        $return[] = $values[0] + $values[1] + floor($values[2] / 2);
                        $return[] = $values[4] + $values[3] + ceil($values[2] / 2);
                        return $return;
                    case 1:
                        $return[] = $values[4] + $values[3] + ceil($values[2] / 2);
                        return $return;
                    case 3:
                        return [$values[0], $values[2], $values[4]];
                }
                return $values;
            }
            return array_fill(0, $steps, 0);
        }

        /*
         |  RENDER :: PANEL
         |  @since  1.0.0
         |
         |  @param  string  The location where the panel gets rendered.
         |  @param  string  The desired design to render.
         |
         |  @return string  The rendered panel HTML content.
         */
        public function render(string $location, string $design = "default"): string {
            global $page;
            global $reactions_plugin;

            // Get Data
            $title = $reactions_plugin->getValue("widget_title");
            $mode = $reactions_plugin->getValue("widget_mode");

            // Render Widget
            ob_start();
            ?>
                <form method="post" action="<?php echo $page->permalink(); ?>?reactions=vote" class="reactions reactions-mode-<?php echo $mode; ?>">
                    <input type="hidden" name="reactions" value="vote" />
                    <?php if($reactions_plugin->getValue("secure_honeypot")) { ?>
                        <input id="reactions-required-email" type="email" name="reactions-email" value="" />
                    <?php } ?>
                    <?php print($this->{"render_$mode"}()); ?>
                </form>
            <?php
            $widget = ob_get_contents();
            ob_end_clean();

            // Render Container
            ob_start();
            if($location === "siteSidebar") {
                ?>
                    <div class="plugin plugin-reactions reactions-<?php echo strtolower($location); ?>">
                        <?php if(!empty($title)) { ?>
                            <h2 class="plugin-label"><?php echo $title; ?></h2>
                        <?php } ?>
                        <div class="plugin-content">
                            <?php print($widget); ?>
                        </div>
                    </div>
                <?php
            } else if($location === "siteBodyEnd") {
                ?>
                    <div class="container container-reactions reactions-<?php echo strtolower($location); ?>">
                        <?php if(!empty($title)) { ?>
                            <h2 class="reactions-title"><?php echo $title; ?></h2>
                        <?php } ?>
                        <?php print($widget); ?>
                    </div>
                <?php
            } else {
                ?>
                    <div class="reactions-panel reactions-<?php echo strtolower($location); ?>">
                        <?php if(!empty($title)) { ?>
                            <h2 class="reactions-title"><?php echo $title; ?></h2>
                        <?php } ?>
                        <?php print($widget); ?>
                    </div>
                <?php
            }
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /*
         |  RENDER :: LIKE|DISLIKE
         |  @since  1.0.0
         */
        protected function render_like(): ?string {
            global $page;
            global $reactions_plugin;

            // Prepare Data
            $like = $reactions_plugin->getValue("reaction_like");
            $votes = $this->votes($page->key(), $like[2]+1);
            $active = $this->voted($page->key());

            // Prepare Like
            $icon = $reactions_plugin->phpPath() . "assets" . DS . "imgs" . DS . "{$like[0]}.svg";
            $icon = explode("\n", file_get_contents($icon));
            $like[0] = implode("\n", array_slice($icon, 1));

            // Prepare Dislike
            if($like[2]) {
                $icon = $reactions_plugin->phpPath() . "assets" . DS . "imgs" . DS . "{$like[3]}.svg";
                $icon = explode("\n", file_get_contents($icon));
                $like[3] = implode("\n", array_slice($icon, 1));
            }

            // Render
            ob_start();
            ?>
                <div class="reactions-ratings columns-<?php echo $like[2]+1; ?>">
                    <div class="rating rating-like">
                        <button class="rating-button active-<?php echo $like[1]; ?> <?php echo ($active === 4)? "active": ""; ?>"  name="reactions-vote" value="4">
                            <div class="button-icon"><?php echo $like[0]; ?></div>
                            <div class="button-count"><?php echo $votes[1] ?? $votes[0]; ?></a>
                        </button>
                    </div>

                    <?php if($like[2]) { ?>
                        <div class="rating rating-dislike">
                            <button class="rating-button active-<?php echo $like[4]; ?> <?php echo ($active === 0)? "active": ""; ?>" name="reactions-vote" value="0">
                                <div class="button-icon"><?php echo $like[3]; ?></div>
                                <div class="button-count"><?php echo $votes[0]; ?></a>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /*
         |  RENDER :: STARS
         |  @since  1.0.0
         */
        protected function render_stars(): ?string {
            global $page;
            global $reactions_plugin;

            // Prepare Data
            $stars = $reactions_plugin->getValue("reaction_stars");
            $votes = $this->votes($page->key(), $stars[3]);
            $active = $this->voted($page->key());

            // Calculate Avaerage
            $calc = 0;
            $total = array_sum($votes);
            foreach($votes AS $num => $votes) {
                $calc += ($num + 1) * $votes;
            }
            $av = ($calc === 0)? 0: $calc / $total;

            // Icon Set
            $icons = $reactions_plugin->phpPath() . "assets" . DS . "imgs" . DS . "{$stars[0]}.svg";
            $icons = explode("\n", file_get_contents($icons)); // Remove XML Tag
            $icons = implode("\n", array_slice($icons, 1));

            // Render
            ob_start();
            ?>
                <div class="reactions-ratings columns-<?php echo $stars[3]; ?>">
                    <?php for($i = $stars[3]-1; $i >= 0; $i--) { ?>
                        <?php
                            $value = $stars[3] > 3? $i: [0 => 0, 1 => 2, 2 => 4][$i];
                            if($av >= ($i + 1)) {
                                $class = "star-full";
                            } else {
                                $class = ceil($av) > $i? "star-half": "star-empty";
                            }
                        ?>
                        <div class="rating rating-star <?php echo $class; ?> active-<?php echo $stars[1]; ?> hover-<?php echo $stars[2]; ?>">
                            <button class="star star-<?php echo ($i+1); ?>" name="reactions-vote" value="<?php echo $value; ?>">
                                <div class="button-icon"><?php echo $icons; ?></div>
                            </button>
                        </div>
                    <?php } ?>
                    <?php if(!empty($total)) { ?>
                        <div class="rating-label">
                            &empty; <?php echo round($av, 1) . s18n__(" out of ") . $total. s18n__(" Votes"); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /*
         |  RENDER :: EMOJIES
         |  @since  1.0.0
         */
        protected function render_emojies(): ?string {
            global $page;
            global $reactions_plugin;

            // Include EmojiOne
            require_once dirname(__DIR__) . DS . "assets" . DS . "php" . DS . "RulesetInterface.php";
            require_once dirname(__DIR__) . DS . "assets" . DS . "php" . DS . "ClientInterface.php";
            require_once dirname(__DIR__) . DS . "assets" . DS . "php" . DS . "Client.php";
            require_once dirname(__DIR__) . DS . "assets" . DS . "php" . DS . "Ruleset.php";
            require_once dirname(__DIR__) . DS . "assets" . DS . "php" . DS . "Emojione.php";

            // Init Emoji One Client
            $client = new Client(new Ruleset());
            $client->sprites = false;
            $client->emojiSize = "128";
            $client->spriteSize = "64";
            $client->imagePathPNG = str_replace("/32/", "/64/", $client->imagePathPNG);

            // Prepare Data
            $votes = $this->votes($page->key());
            $active = $this->voted($page->key());

            // Render
            ob_start();
            ?>
                <div class="reactions-ratings columns-5">
                    <?php foreach($reactions_plugin->getValue("reaction_emojies") AS $num => $emoji) { ?>
                        <?php [$emoji, $label] = array_values($emoji); ?>
                        <div class="rating rating-emoji-<?php echo $num; ?>">
                            <button type="submit" name="reactions-vote" value="<?php echo $num; ?>" class="emoji <?php echo ($active === $num)? "active": ""; ?>">
                                <div class="emoji-image"><?php echo $client->shortnameToImage($emoji); ?></div>
                                <?php if(!empty($label)) { ?>
                                    <div class="emoji-label"><?php echo $label; ?></div>
                                <?php } ?>
                                <div class="emoji-count"><?php echo $votes[$num]; ?></div>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }
