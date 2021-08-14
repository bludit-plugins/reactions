<?php
declare(strict_types=1);
/*
 |  Reactions   Let your users react to your content.
 |  @file       ./plugin.php
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.1 [0.1.0] - Stable
 |
 |  @website    https://github.com/pytesNET/reactions
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */
    defined("BLUDIT") or die("Go directly to Jail. Do not pass Go. Do not collect 200 Cookies!");


    // Init Main Functions
    require_once __DIR__ . DS . "system" . DS . "functions.php";


    // Load Plus Package
    if(file_exists(dirname(__FILE__) . DS . "plugin-plus.php")) {
        require_once "plugin-plus.php";
    } else if(!defined("REACTIONS_PLUS")) {
        define("REACTIONS_PLUS", false);
    }


    // Init Plugin Class
    class ReactionsPlugin extends Plugin {
        const VERSION = "1.0.0";
        const STATUS = "Stable";

        /*
         |  ACTIVATE FRONTEND RENDERING
         */
        public $frontend = false;


        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         */
        public function __construct() {
            global $reactions_plugin;
            $reactions_plugin = $this;      // Attach Plugin
            parent::__construct();          // Call Parent
        }

        /*
         |  PLUGIN :: INIT
         |  @since  0.1.0
         */
        public function init(): void {
            $this->dbFields = [
                "version"           => self::VERSION,                   // Current Version
                "position"          => 1,                               // Sidebar Position
                "widget_title"      => "Your Reaction to this Post?",   // The Widget Title
                "widget_mode"       => "emojies",                       // The Reactions Mode
                "reaction_like"     => [                                // Settings for Like | Dislike
                    "thumbs-o-up", "green", false, "thumbs-o-down", "red"
                ],
                "reaction_stars"    => [                                // Settings for Stars
                    "stars", "yellow", "red", 5
                ],
                "reaction_emojies"  => [                                // Settings for Emojies
                    [":heart_eyes:", "Love"],
                    [":stuck_out_tongue_closed_eyes:", "Funny"],
                    [":hushed:", "Wow"],
                    [":cry:", "Sad"],
                    [":rage:", "Angry"]
                ],
                "frontend_hook"     => "pageEnd",                       // Used page hook
                "frontend_ajax"     => true,                            // AJAX Submit
                "frontend_design"   => "default",                       // Embed Stylesheet
                "show_on_published" => true,                            // Enable on Published Posts
                "show_on_sticky"    => true,                            // Enable on Sticky Posts
                "show_on_static"    => false,                           // Enable on Static Posts
                "hide_on_comments"  => true,                            // Hide if comments are disabled
                "secure_cookies"    => true,                            // Secure up w\ Test Cooky
                "secure_honeypot"   => true,                            // Secure up w\ Honey-Pot
                "secure_filter"     => true,                            // Filter using HTTP Stuff
                "content_view"      => false,                           // EXPERIMTENAL Content List Hack
                "log_ratings"       => true,                            // [PLUS] Log Ratings
                "log_changes"       => false,                           // [PLUS] Log Changes
                "store_cookie"      => false,                           // [PLUS] Store as Cookie
                "store_server"      => false,                           // [PLUS] Store on Server
                "gdpr_cookie_key"   => "",                              // [PLUS] GDPR Cookie Key
                "gdpr_cookie_value" => ""                               // [PLUS] GDPR Cookie Value
            ];
        }

        /*
         |  PLUGIN :: INSTALL
         |  @since  1.0.0
         */
        public function install($position = 1): bool {
            if($this->installed()) {
                return false;
            }

            // Create Workspace
            $workspace = $this->workspace();
            mkdir($workspace, DIR_PERMISSIONS, true);
    		mkdir(PATH_PLUGINS_DATABASES . $this->directoryName, DIR_PERMISSIONS, true);

            // Store Defaults
            $this->db = $this->dbFields;
            return $this->save();
        }

        /*
         |  PLUGIN :: INSTALLED
         |  @since  1.0.0
         */
        public function installed(): bool {
            global $reactions;
            global $reactions_logs;
            global $reactions_users;

            if(file_exists($this->filenameDb)) {
                if(!defined("REACTIONS")) {
                    define("REACTIONS", self::VERSION);
                    define("REACTIONS_WS", $this->workspace());
                    return true;
                } else {
                    if(version_compare($this->db["version"] ?? "0.1.3", self::VERSION, "<")) {
                        $this->update($this->db["version"] ?? "0.1.3");
                    }
                }

                // Init Reactions Class
                if(!class_exists("Reactions")) {
                    require_once "system" . DS . "reactions.php";

                    if(REACTIONS_PLUS) {
                        require_once "system" . DS . "reactions-plus.php";
                        $reactions = new ReactionsPlus();
                    } else {
                        $reactions = new Reactions();
                    }
                }

                // Init Log Class
                if(REACTIONS_PLUS && !class_exists("ReactionsLogs")) {
                    require_once "system" . DS . "reactions-logs.php";
                    $reactions_logs = new ReactionsLogs();
                }

                // Init Rating Class
                if(REACTIONS_PLUS && !class_exists("ReactionsUsers")) {
                    require_once "system" . DS . "reactions-users.php";
                    $reactions_users = new ReactionsUsers();
                }
            }
            return file_exists($this->filenameDb);
        }

        /*
         |  PLUGIN :: UPDATE
         |  @since  1.0.0
         */
        protected function update(string $version): void {
            global $pages;
            global $reactions;

            // Init Reactions Class
            if(!class_exists("Reactions")) {
                require_once "system" . DS . "reactions.php";
                $reactions = new Reactions();
            }

            // Upgrade from 0.1.x to 1.0.0
            if(version_compare($version, "0.2.0", "<")) {
                $old = $this->db;
                $new = $this->dbFields;

                // Parse New
                $new["position"] = $old["position"];
                $new["widget_title"] = $old["emojies_title"];
                $new["widget_mode"] = "emojies";
                $new["reaction_emojies"][0] = array_values($old["emojies"][0]);
                $new["reaction_emojies"][1] = array_values($old["emojies"][1]);
                $new["reaction_emojies"][2] = array_values($old["emojies"][2]);
                $new["reaction_emojies"][3] = array_values($old["emojies"][3]);
                $new["reaction_emojies"][4] = array_values($old["emojies"][4]);
                $new["frontend_hook"] = ($old["frontend_hook"] === "inject")? ":after": $old["frontend_hook"];

                // Store
                $this->db = $new;
                $this->save();

                // Move Current Reactions
                $copy = $pages->db;
                $change = false;
                foreach($pages->db AS $slug => &$page) {
                    if(!isset($page["custom"])) {
                        continue;
                    }
                    if(!isset($page["custom"]["reactions"])) {
                        continue;
                    }
                    $reactions->db[$slug] = implode(",", $page["custom"]["reactions"]);
                    unset($page["custom"]["reactions"]);

                    if(!$change) {
                        $change = true;
                    }
                }

                // Create Backup
                if($change) {
                    $backup = new dbJSON(PATH_TMP . "pages.temp.php");
                    $backup->db = $copy;

                    // Store Cleaned DB
                    try {
                        $status = @$backup->save();
                    } catch(Exception $e) {
                        $status = false;
                    }
                    if($status) {
                        $pages->save();
                        $reactions->save();
                    } else {
                        deactivatePlugin(__CLASS__);
                        Alert::set(s18n__("The Reactions Plugin could not be updated, please check the requirements or contact us!"), ALERT_STATUS_FAIL);
                        header("Location: " . rtrim(DOMAIN_ADMIN, "/") . "/plugins");
                        die();
                    }
                }
            }
        }

        /*
         |  BACKEND :: RENDER ADMIN FORM
         |  @since  0.1.0
         */
        public function post(): bool {
            $data = $_POST;
            foreach($this->dbFields AS $key => $value) {
                if(in_array($key, ["widget_title", "gdpr_cookie_key", "gdpr_cookie_value"])) {
                    $this->db[$key] = $data[$key] ?? $this->db[$key];
                }
                if($key === "widget_mode" && in_array($data[$key] ?? "", ["like", "stars", "emojies"])) {
                    $this->db[$key] = $data[$key];
                }

                // Like | Dislike Rating
                if($key === "reaction_like") {
                    $this->db[$key] = [
                        $data['reaction_like'][0] ?? "thumbs-o-up",
                        $data['reaction_like'][1] ?? "green",
                        ($data['reaction_like'][2] ?? "false") === "true"? true: false,
                        $data['reaction_like'][3] ?? "thumbs-o-down",
                        $data['reaction_like'][4] ?? "red"
                    ];
                }

                // Stars Rating
                if($key === "reaction_stars" && count($data[$key]) >= 3) {
                    $this->db[$key] = [
                        $data['reaction_stars'][0] ?? "stars",
                        $data['reaction_stars'][1] ?? "yellow",
                        $data['reaction_stars'][2] ?? "red",
                        ($data['reaction_stars'][3] ?? "5") === "5"? 5: 3
                    ];
                }

                // Emojies Rating
                if($key === "reaction_emojies" && !empty($data[$key])) {
                    $rating = [];
                    for($i = 0; $i < 5; $i++) {
                        if(($data[$key][$i]["emoji"] ?? "") !== "") {
                            $rating[] = array_values($data[$key][$i]);
                        }
                    }
                    $this->db[$key] = $rating;
                }

                // Booleans
                if(is_bool($value)) {
                    $this->db[$key] = ($data[$key] ?? "false") === "true";
                }

                // Frontend Hooks
                $hooks = ["disabled", "pageBegin", "pageEnd", "siteSidebar", "siteBodyEnd", ":before", ":after", ":variable"];
                if($key === "frontend_hook" && in_array($data[$key], $hooks)) {
                    $this->db[$key] = $data[$key];
                }

                // Frontend Designs
                $designs = ["none", "default"];
                if($key === "frontend_design" && in_array($data[$key], $designs)) {
                    $this->db[$key] = $data[$key];
                }
            }

            // Remove Backup
            if(file_exists(PATH_TMP . "pages.temp.php")) {
                unlink(PATH_TMP . "pages.temp.php");
            }

            // Handle
            return $this->save();
        }

        /*
         |  BACKEND :: RENDER ADMIN HEADER
         |  @since  0.1.0
         */
        public function adminHead(): ?string {
            global $url;
	        global $published;
            global $static;
            global $sticky;
            global $reactions;

            // Check Content Page
            if(stripos($url->slug(), "content") === 0 && $this->getValue("content_view")) {
                $mode = $this->getValue("widget_mode");

                // Build List
                $list = [];
                if($this->getValue("show_on_published")) {
                    $list = array_merge($list, $published);
                }
                if($this->getValue("show_on_static")) {
                    $list = array_merge($list, $static);
                }
                if($this->getValue("show_on_sticky")) {
                    $list = array_merge($list, $sticky);
                }
                if(empty($list)) {
                    return "";
                }

                // Render
                ob_start();
                ?>
                    <script type="text/javascript">
                        window.REACTIONS_DATA = {
                            <?php foreach($list AS $slug) { ?>
                                <?php
                                    if($mode === "like") {
                                        if($this->getValue("reaction_like")[2]) {
                                            $data = $reactions->votes($slug, 2);
                                            $output = $data[1] . " / " . $data[0];
                                        } else {
                                            $data = $reactions->votes($slug, 1);
                                            $output = $data[0];
                                        }
                                    } else if($mode === "stars") {
                                        $data = $reactions->votes($slug, $this->getValue("reaction_stars")[3]);

                                        // Calc Average
                                        $calc = 0;
                                        $total = array_sum($data);
                                        foreach($data AS $num => $votes) {
                                            $calc += ($num + 1) * $votes;
                                        }
                                        $av = ($calc === 0)? 0: $calc / $total;

                                        // Output
                                        $output = ($calc === 0)? "Unrated": "&empty; " . $av . " of " . $total;
                                    } else {
                                        $output = array_sum($reactions->votes($slug));
                                    }
                                ?>
                                "<?php echo $slug; ?>": '<?php echo $output; ?>',
                            <?php } ?>
                        };
                    </script>
                    <script type="text/javascript" src="<?php echo $this->domainPath(); ?>/system/js/admin.content.js?ver=<?php echo self::VERSION; ?>"></script>
                <?php
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            }

            // Check Dashboard Page
            if(REACTIONS_PLUS && (strlen($url->slug()) === 0 || stripos($url->slug(), "dashboard") === 0)) {
                ob_start();
                ?>
                    <style type="text/css">
                        #dashboard .reactions-plus-widget img.emojione {
                            width: 16px;
                            height: auto;
                            margin-top: -2px;
                        }
                    </style>
                <?php
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            }

            // Check Reactions Page
            if(stripos($url->slug(), "Reactions") === false) {
                return null;
            }

            // Render
            ob_start();
            ?>
                <link type="text/css" rel="stylesheet" href="<?php echo $this->domainPath(); ?>/assets/css/emojionearea.min.css?ver=3.4.1" />
                <link type="text/css" rel="stylesheet" href="<?php echo $this->domainPath(); ?>/system/css/admin.reactions.css?ver=<?php echo self::VERSION; ?>" />
                <script type="text/javascript" src="<?php echo $this->domainPath(); ?>/assets/js/emojionearea.min.js?ver=3.4.1"></script>
                <script type="text/javascript" src="<?php echo $this->domainPath(); ?>/system/js/admin.reactions.js?ver=<?php echo self::VERSION; ?>"></script>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /*
         |  BACKEND :: RENDER ADMIN FORM
         |  @since  0.1.0
         */
        public function form(): void {
            include "system" . DS . "admin.php";
        }

        /*
         |  BACKEND :: DASHBOARD
         |  @since  1.0.0
         */
        public function dashboard(): string {
            global $reactions_logs;

            if(REACTIONS_PLUS && !empty($reactions_logs) && $this->getValue("log_ratings")) {
                return $reactions_logs->widget();
            }
            return "";
        }

        /*
         |  FRONTEND :: SITE HEADER
         |  @since  0.1.0
         */
        public function beforeSiteLoad(): void {
            global $page;
            global $url;
            global $reactions;

            // Check Current Page
            if($url->whereAmI() !== "page" || empty($page->uuid())) {
                return;
            }
            if(!$reactions->votable($page)) {
                return;
            }
            $this->frontend = true;

            // Session
            if(!Session::started()){
                Session::start();
            }

            // Handle Vote
            if(($_POST["reactions"] ?? "") === "vote") {
                $status = $reactions->vote($page->key(), (int) $_POST["reactions-vote"]);

                if(($_SERVER["HTTP_X_REQUESTED_WITH"] ?? "") === "XMLHttpRequest") {
                    $this->frontend = true;

                    $response = [];
                    if($status) {
                        $response["status"] = "success";
                        $response["message"] = $this->_render($this->getValue("frontend_hook"));
                    } else {
                        $response["status"] = "error";
                        $response["message"] = null;
                    }

                    print(json_encode($response));
                    die();
                } else {
                    header("Location: {$page->permalink()}?reaction=" . ($status? "success": "error"));
                }
                die();
            }

            // Prepare Cookies Check
            if($this->getValue("secure_cookies") && Session::get("REACTIONS-KEY") === false) {
                Session::set("REACTIONS-KEY", md5(session_id()));
            }

            // Handle Injection
            switch($this->getValue("frontend_hook")) {
                case ":before":
                    $page->setField("content", $reactions->render(":before", $this->getValue("frontend_design")) . "\n" . $page->content());
                    break;
                case ":after":
                    $page->setField("content", $page->content() . "\n" . $reactions->render(":after", $this->getValue("frontend_design")));
                    break;
                case ":variable":
                    $page->setField("content", str_replace("[reactions]", $reactions->render(":variable", $this->getValue("frontend_design")), $page->content()));
                    break;
            }
        }

        /*
         |  FRONTEND :: SITE HEADER
         |  @since  0.1.0
         */
        public function siteHead(): ?string {
            if($this->frontend === false) {
                return null;
            }

            ob_start();
            if($this->getValue("frontend_design") === "default") {
                ?><link type="text/css" rel="stylesheet" href="<?php echo $this->domainPath(); ?>system/css/reactions.css?ver=<?php echo self::VERSION; ?>" /><?php
            }
            if($this->getValue("frontend_ajax")) {
                ?><script type="text/javascript" src="<?php echo $this->domainPath(); ?>system/js/reactions.js?ver=<?php echo self::VERSION; ?>"></script><?php
            }
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /*
         |  FRONTEND :: PAGE BEGIN
         |  @since  1.0.0
         */
        public function pageBegin(): ?string {
            return $this->_render("pageBegin");
        }

        /*
         |  FRONTEND :: PAGE END
         |  @since  0.1.0
         */
        public function pageEnd(): ?string {
            return $this->_render("pageEnd");
        }

        /*
         |  FRONTEND :: SITE SIDEBAR
         |  @since  1.0.0
         */
        public function siteSidebar(): ?string {
            return $this->_render("siteSidebar");
        }

        /*
         |  FRONTEND :: SITE BODY END
         |  @since  0.1.0
         */
        public function siteBodyEnd(): ?string {
            return $this->_render("siteBodyEnd");
        }

        /*
         |  FRONTEND :: RENDER PANEL
         |  @since  1.0.0
         */
        private function _render(string $hook): ?string {
            if(!$this->frontend || $this->getValue("frontend_hook") !== $hook) {
                return null;
            }
            global $reactions;
            return $reactions->render($hook, $this->getValue("frontend_design"));
        }
    }
