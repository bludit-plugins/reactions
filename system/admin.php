<?php
/*
 |  Reactions   Let your users react to your content.
 |  @file       ./system/admin.php
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    1.0.1 [1.0.0] - Stable
 |
 |  @website    https://github.com/pytesNET/reactions
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 - 2020 pytesNET <info@pytes.net>
 */
    defined("BLUDIT") or die("Go directly to Jail. Do not pass Go. Do not collect 200 Cookies!");

    // Allowed Colours
    $colors = [
        "black" => s18n__("Black"),
        "red" => s18n__("Red"),
        "orange" => s18n__("Orange"),
        "yellow" => s18n__("Yellow"),
        "green" => s18n__("Green"),
        "blue" => s18n__("Blue"),
        "violet" => s18n__("Violet")
    ];
?>
<?php if(file_exists(PATH_TMP . "pages.temp.php")) { ?>
    <div class="form-group row mt-2 mb-2">
        <label class="col-2 col-form-label text-danger"><?php s18n_e("Backup File"); ?></label>
        <div class="col-10 bg-danger text-white rounded mt-2 p-2">
            <?php s18n_e("A previous version of the Reactions Plugin has been detected."); ?>
            <?php s18n_e("To make sure nothing gets lost, a backup of your pages file has been created."); ?>
            <?php echo strtr(s18n__("This file gets automatically deleted from the ':path' directory when you click 'Save'."), [":path" => PATH_TMP]); ?><br />
        </div>
        <div class="col-10 offset-2 font-weight-bold text-danger mt-2">
            <?php s18n_e("Please check if your Website doesn't show any error, before you delete your Backup!"); ?>
        </div>
    </div>
<?php } ?>

<div class="form-group row">
    <label for="widget-title" class="col-2 col-form-label"><?php s18n_e("Reactions Title"); ?></label>
    <div class="col-10">
        <div class="pt-3">
            <input type="text" id="widget-title" name="widget_title" value="<?php echo $this->getValue("widget_title"); ?>" class="form-control" placeholder="<?php s18n_e("Reactions Widget Title"); ?>" />
            <span class="tip"><?php s18n_e("Keep this field empty, to disable the title completely."); ?></span>
        </div>
    </div>
</div>

<?php $mode = $this->getValue("widget_mode"); ?>
<input type="hidden" name="widget_mode" value="<?php echo $mode; ?>" />
<div class="reactions-mode form-group row pr-3 my-5">
    <div class="nav flex-column col-2 mr-0 pr-0">
        <a href="#reactions-mode-like" class="nav-item nav-link bg-white border <?php echo $mode === "like"? "active": ""; ?>" data-toggle="tab"><?php s18n_e("Like / Dislike"); ?></a>
        <a href="#reactions-mode-stars" class="nav-item nav-link bg-white border <?php echo $mode === "stars"? "active": ""; ?>" data-toggle="tab"><?php s18n_e("Stars"); ?></a>
        <a href="#reactions-mode-emojies" class="nav-item nav-link bg-white border <?php echo $mode === "emojies"? "active": ""; ?>" data-toggle="tab"><?php s18n_e("Emojies"); ?></a>
    </div>
    <div class="tab-content col-10 border bg-white rounded-right ml-0 pl-0">
        <?php $like = $this->getValue("reaction_like"); ?>
        <div id="reactions-mode-like" class="tab-pane fade <?php echo $mode === "like"? "show active": ""; ?>">
            <input type="hidden" name="reaction_like[0]" value="<?php echo $like[0]; ?>" data-reaction-value="like-icon" />
            <input type="hidden" name="reaction_like[1]" value="<?php echo $like[1]; ?>" data-reaction-value="like-color" />
            <input type="hidden" name="reaction_like[2]" value="false" data-reaction-value="dislike" />
            <input type="hidden" name="reaction_like[3]" value="<?php echo $like[3]; ?>" data-reaction-value="dislike-icon" />
            <input type="hidden" name="reaction_like[4]" value="<?php echo $like[4]; ?>" data-reaction-value="dislike-color" />

            <div class="row px-3 py-4">
                <div class="col-2 offset-2 text-center text-muted pt-1 pb-3"><?php s18n_e("Active Color"); ?></div>
                <div class="col-2 text-center text-muted pt-1 pb-3"><?php s18n_e("Icon"); ?></div>
                <div class="col-4 text-center text-muted pb-3" style="margin-top:-5px;">
                    <div class="border rounded bg-light py-2 px-3 text-left">
                        <div class="custom-control custom-checkbox">
                            <input id="reaction-dislike" type="checkbox" name="reaction_like[2]" value="true" class="custom-control-input" <?php bt_checked($like[2]); ?> />
                            <label class="custom-control-label" for="reaction-dislike" style="margin-top:0!important;line-height:1.4rem;"> <?php s18n_e("Enable"); ?></label>
                        </div>
                    </div>
                </div>

                <!-- Like :: Active Color -->
                <div class="col-2 offset-2 text-center" data-reaction-form="like-color">
                    <div class="reaction-select">
                        <div class="select-value"><span class="value-dot" data-color="<?php echo $like[1]; ?>"></span> <?php echo $colors[$like[1]]; ?></div>
                        <div class="select-toggle dropdown-toggle" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <?php foreach($colors AS $color => $label) { ?>
                                <a href="#like-color:<?php echo $color; ?>" class="dropdown-item">
                                    <span class="value-dot" data-color="<?php echo $color; ?>"></span> <?php echo $label; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Like :: Icon -->
                <div class="col-2 text-center" data-reaction-form="like-icon">
                    <div class="reaction-select">
                        <div class="select-value">
                            <span class="value-icon">
                                <img src="<?php echo $this->domainPath(); ?>assets/imgs/<?php echo $like[0]; ?>.svg" />
                            </span>
                        </div>
                        <div class="select-toggle dropdown-toggle" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <a href="#like-icon:thumbs-o-up" class="dropdown-item <?php echo $like[0] === "thumbs-o-up"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/thumbs-o-up.svg" />
                                </span> <?php s18n_e("Thumbs Up"); ?>
                            </a>
                            <a href="#like-icon:thumbs-up" class="dropdown-item <?php echo $like[0] === "thumbs-up"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/thumbs-up.svg" />
                                </span> <?php s18n_e("Thumbs Up"); ?>
                            </a>
                            <a href="#like-icon:arrow-circle-up" class="dropdown-item <?php echo $like[0] === "arrow-circle-up"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/arrow-circle-up.svg" />
                                </span> <?php s18n_e("Arrow Up"); ?>
                            </a>
                            <a href="#like-icon:arrow-up" class="dropdown-item <?php echo $like[0] === "arrow-up"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/arrow-up.svg" />
                                </span> <?php s18n_e("Arrow Up"); ?>
                            </a>
                            <a href="#like-icon:smile-o" class="dropdown-item <?php echo $like[0] === "smile-o"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/smile-o.svg" />
                                </span> <?php s18n_e("Smile"); ?>
                            </a>
                            <a href="#like-icon:heart" class="dropdown-item <?php echo $like[0] === "heart"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/heart.svg" />
                                </span> <?php s18n_e("Heart"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dislike :: Icon -->
                <div class="col-2 text-center" data-reaction-form="dislike-icon">
                    <div class="reaction-select <?php echo (!$like[2])? "disabled": ""; ?>">
                        <div class="select-value">
                            <span class="value-icon">
                                <img src="<?php echo $this->domainPath(); ?>assets/imgs/<?php echo $like[3]; ?>.svg" />
                            </span>
                        </div>
                        <div class="select-toggle dropdown-toggle <?php echo (!$like[2])? "disabled": ""; ?>" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <a href="#dislike-icon:thumbs-o-down" class="dropdown-item <?php echo $like[3] === "thumbs-o-down"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/thumbs-o-down.svg" />
                                </span> <?php s18n_e("Thumbs Down"); ?>
                            </a>
                            <a href="#dislike-icon:thumbs-down" class="dropdown-item <?php echo $like[3] === "thumbs-down"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/thumbs-down.svg" />
                                </span> <?php s18n_e("Thumbs Down"); ?>
                            </a>
                            <a href="#dislike-icon:arrow-circle-down" class="dropdown-item <?php echo $like[3] === "arrow-circle-down"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/arrow-circle-down.svg" />
                                </span> <?php s18n_e("Arrow Down"); ?>
                            </a>
                            <a href="#dislike-icon:arrow-down" class="dropdown-item <?php echo $like[3] === "arrow-down"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/arrow-down.svg" />
                                </span> <?php s18n_e("Arrow Down"); ?>
                            </a>
                            <a href="#dislike-icon:frown-o" class="dropdown-item <?php echo $like[3] === "frown-o"? "active": ""; ?>">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/frown-o.svg" />
                                </span> <?php s18n_e("Frown"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dislike :: Active Color -->
                <div class="col-2 text-center" data-reaction-form="dislike-color">
                    <div class="reaction-select <?php echo (!$like[2])? "disabled": ""; ?>">
                        <div class="select-value"><span class="value-dot" data-color="<?php echo $like[4]; ?>"></span> <?php echo $colors[$like[4]]; ?></div>
                        <div class="select-toggle dropdown-toggle <?php echo (!$like[2])? "disabled": ""; ?>" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <?php foreach($colors AS $color => $label) { ?>
                                <a href="#dislike-color:<?php echo $color; ?>" class="dropdown-item">
                                    <span class="value-dot" data-color="<?php echo $color; ?>"></span> <?php echo $label; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $stars = $this->getValue("reaction_stars"); ?>
        <div id="reactions-mode-stars" class="tab-pane fade <?php echo $mode === "stars"? "show active": ""; ?>">
            <input type="hidden" name="reaction_stars[0]" value="<?php echo $stars[0]; ?>" data-reaction-value="iconset" />
            <input type="hidden" name="reaction_stars[1]" value="<?php echo $stars[1]; ?>" data-reaction-value="active-color" />
            <input type="hidden" name="reaction_stars[2]" value="<?php echo $stars[2]; ?>" data-reaction-value="hover-color" />
            <input type="hidden" name="reaction_stars[3]" value="<?php echo $stars[3]; ?>" data-reaction-value="amount" />

            <div class="row px-3 py-4">
                <div class="col-2 offset-2 text-center text-muted pt-1 pb-3"><?php s18n_e("Icon Set"); ?></div>
                <div class="col-2 text-center text-muted pt-1 pb-3"><?php s18n_e("Active Color"); ?></div>
                <div class="col-2 text-center text-muted pt-1 pb-3"><?php s18n_e("Hover Color"); ?></div>
                <div class="col-2 text-center text-muted pt-1 pb-3"><?php s18n_e("Amount"); ?></div>

                <!-- Icon Set -->
                <div class="col-2 offset-2 text-center pt-2" data-reaction-form="iconset">
                    <div class="reaction-select">
                        <div class="select-value">
                            <span class="value-icon">
                                <img src="<?php echo $this->domainPath(); ?>assets/imgs/star.svg" />
                            </span> <?php s18n_e("Stars"); ?>
                        </div>
                        <div class="select-toggle dropdown-toggle" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <a href="#iconset:stars" class="dropdown-item">
                                <span class="value-icon">
                                    <img src="<?php echo $this->domainPath(); ?>assets/imgs/star.svg" />
                                </span> <?php s18n_e("Stars"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Active Color -->
                <div class="col-2 text-center pt-2" data-reaction-form="active-color">
                    <div class="reaction-select">
                        <div class="select-value"><span class="value-dot" data-color="<?php echo $stars[1]; ?>"></span> <?php echo $colors[$stars[1]]; ?></div>
                        <div class="select-toggle dropdown-toggle" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <?php foreach($colors AS $color => $label) { ?>
                                <a href="#active-color:<?php echo $color; ?>" class="dropdown-item">
                                    <span class="value-dot" data-color="<?php echo $color; ?>"></span> <?php echo $label; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Hover Color -->
                <div class="col-2 text-center pt-2" data-reaction-form="hover-color">
                    <div class="reaction-select">
                        <div class="select-value"><span class="value-dot" data-color="<?php echo $stars[2]; ?>"></span> <?php echo $colors[$stars[2]]; ?></div>
                        <div class="select-toggle dropdown-toggle" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <?php foreach($colors AS $color => $label) { ?>
                                <a href="#hover-color:<?php echo $color; ?>" class="dropdown-item">
                                    <span class="value-dot" data-color="<?php echo $color; ?>"></span> <?php echo $label; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Amount -->
                <div class="col-2 text-center pt-2" data-reaction-form="amount">
                    <div class="reaction-select">
                        <div class="select-value value-single"><?php echo $stars[3]; ?> <?php s18n_e("Stars"); ?></div>
                        <div class="select-toggle dropdown-toggle" data-toggle="dropdown"></div>

                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                            <a href="#amount:3" class="dropdown-item">3 <?php s18n_e("Stars"); ?></a>
                            <a href="#amount:5" class="dropdown-item">5 <?php s18n_e("Stars"); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $emojies = $this->getValue("reaction_emojies"); ?>
        <div id="reactions-mode-emojies" class="tab-pane fade <?php echo $mode === "emojies"? "show active": ""; ?>">
            <div class="row px-5 py-4">
                <div class="col text-center">
                    <div id="emoji-one" class="emoji-input mb-3" data-emoji-placeholder="<?php echo $emojies[0][0]; ?>" style="height:34px;"></div>
                    <input type="hidden" name="reaction_emojies[0][emoji]" value="<?php echo $emojies[0][0]; ?>" />
                    <input type="text" class="form-control-sm" name="reaction_emojies[0][label]" value="<?php echo $emojies[0][1]; ?>" placeholder="<?php s18n_e("Emotion Text"); ?>" />
                </div>
                <div class="col text-center">
                    <div id="emoji-two" class="emoji-input mb-3" data-emoji-placeholder="<?php echo $emojies[1][0]; ?>" style="height:34px;"></div>
                    <input type="hidden" name="reaction_emojies[1][emoji]" value="<?php echo $emojies[1][0]; ?>" />
                    <input type="text" class="form-control-sm" name="reaction_emojies[1][label]" value="<?php echo $emojies[1][1]; ?>" placeholder="<?php s18n_e("Emotion Text"); ?>" />
                </div>
                <div class="col text-center">
                    <div id="emoji-three" class="emoji-input mb-3" data-emoji-placeholder="<?php echo $emojies[2][0]; ?>" style="height:34px;"></div>
                    <input type="hidden" name="reaction_emojies[2][emoji]" value="<?php echo $emojies[2][0]; ?>" />
                    <input type="text" class="form-control-sm" name="reaction_emojies[2][label]" value="<?php echo $emojies[2][1]; ?>" placeholder="<?php s18n_e("Emotion Text"); ?>" />
                </div>
                <div class="col text-center">
                    <div id="emoji-four" class="emoji-input mb-3" data-emoji-placeholder="<?php echo $emojies[3][0]; ?>" style="height:34px;"></div>
                    <input type="hidden" name="reaction_emojies[3][emoji]" value="<?php echo $emojies[3][0]; ?>" />
                    <input type="text" class="form-control-sm" name="reaction_emojies[3][label]" value="<?php echo $emojies[3][1]; ?>" placeholder="<?php s18n_e("Emotion Text"); ?>" />
                </div>
                <div class="col text-center">
                    <div id="emoji-five" class="emoji-input mb-3" data-emoji-placeholder="<?php echo $emojies[4][0]; ?>" style="height:34px;"></div>
                    <input type="hidden" name="reaction_emojies[4][emoji]" value="<?php echo $emojies[4][0]; ?>" />
                    <input type="text" class="form-control-sm" name="reaction_emojies[4][label]" value="<?php echo $emojies[4][1]; ?>" placeholder="<?php s18n_e("Emotion Text"); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-9 offset-2 pt-1">
        <span class="tip"><span class="text-danger"><?php s18n_e("You can switch between the modes at any time, just keep in mind that each mode stores the votes separately."); ?></span></span>
    </div>
</div>

<div class="mt-5 mb-4"><hr /></div>

<div class="form-group row">
    <label for="frontend-hook" class="col-2 col-form-label"><?php s18n_e("Injection"); ?></label>
    <div class="col-10 pt-3">
        <select id="frontend-hook" name="frontend_hook" class="custom-select">
            <option value="disabled" <?php bt_selected($this->getValue("frontend_hook"), "disable"); ?>><?php s18n_e("Manual Injection"); ?></option>
            <optgroup label="<?php s18n_e("Use Theme Hooks"); ?>">
                <option value="pageBegin" <?php bt_selected($this->getValue("frontend_hook"), "pageBegin"); ?>><?php s18n_e('Use "pageBegin" Hook'); ?></option>
                <option value="pageEnd" <?php bt_selected($this->getValue("frontend_hook"), "pageEnd"); ?>><?php s18n_e('Use "pageEnd" Hook'); ?></option>
                <option value="siteSidebar" <?php bt_selected($this->getValue("frontend_hook"), "siteSidebar"); ?>><?php s18n_e('Use "siteSidebar" Hook'); ?></option>
                <option value="siteBodyEnd" <?php bt_selected($this->getValue("frontend_hook"), "siteBodyEnd"); ?>><?php s18n_e('Use "siteBodyEnd" Hook'); ?></option>
            </optgroup>
            <optgroup label="<?php s18n_e("Modify Page Content"); ?>">
                <option value=":before" <?php bt_selected($this->getValue("frontend_hook"), ":before"); ?>><?php s18n_e("Inject before Content"); ?></option>
                <option value=":after" <?php bt_selected($this->getValue("frontend_hook"), ":after"); ?>><?php s18n_e("Inject after Content"); ?></option>
                <option value=":variable" <?php bt_selected($this->getValue("frontend_hook"), ":variable"); ?>><?php s18n_e("Use Magic Content Variable"); ?></option>
            </optgroup>
        </select>

        <div class="custom-control custom-checkbox reactions-control">
            <input id="frontend-ajax" type="checkbox" name="frontend_ajax" value="true" class="custom-control-input" <?php bt_checked($this->getValue("frontend_ajax")); ?>/>
            <label for="frontend-ajax" class="custom-control-label mt-0"> <?php s18n_e("Enable Submit via AJAX"); ?></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label for="frontend-design" class="col-2 col-form-label"><?php s18n_e("Design"); ?></label>
    <div class="col-10 pt-3">
        <select id="frontend-design" name="frontend_design" class="custom-select">
            <option value="none" <?php bt_selected($this->getValue("frontend_design"), "none"); ?>><?php s18n_e("No Design"); ?></option>
            <option value="default" <?php bt_selected($this->getValue("frontend_design"), "default"); ?>><?php s18n_e("Minimalistic Design"); ?></option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-2 col-form-label"><?php s18n_e("Visibility"); ?></label>
    <div class="col-10 pt-3">
        <div class="custom-control custom-checkbox reactions-control">
            <input id="show-on-published" type="checkbox" name="show_on_published" value="true" class="custom-control-input" <?php bt_checked($this->getValue("show_on_published")); ?> />
            <label for="show-on-published" class="custom-control-label mt-0"> <?php s18n_e("Show on Published Pages"); ?></label>
        </div><br />

        <div class="custom-control custom-checkbox reactions-control">
            <input id="show-on-sticky" type="checkbox" name="show_on_sticky" value="true" class="custom-control-input" <?php bt_checked($this->getValue("show_on_sticky")); ?> />
            <label for="show-on-sticky" class="custom-control-label mt-0"> <?php s18n_e("Show on Sticky Pages"); ?></label>
        </div><br />

        <div class="custom-control custom-checkbox reactions-control">
            <input id="show-on-static" type="checkbox" name="show_on_static" value="true" class="custom-control-input" <?php bt_checked($this->getValue("show_on_static")); ?> />
            <label for="show-on-static" class="custom-control-label mt-0"> <?php s18n_e("Show on Static Pages"); ?></label>
        </div><br />

        <div class="custom-control custom-checkbox reactions-control">
            <input id="hide-on-comments" type="checkbox" name="hide_on_comments" value="true" class="custom-control-input" <?php bt_checked($this->getValue("hide_on_comments")); ?> />
            <label for="hide-on-comments" class="custom-control-label mt-0"> <?php s18n_e("Hide if Comments are disabled"); ?></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-2 col-form-label"><?php s18n_e("Protection"); ?></label>
    <div class="col-10 pt-3">
        <div class="custom-control custom-checkbox reactions-control">
            <input id="secure-cookies" type="checkbox" name="secure_cookies" value="true" class="custom-control-input" <?php bt_checked($this->getValue("secure_cookies")); ?> />
            <label for="secure-cookies" class="custom-control-label mt-0"> <?php s18n_e("Session Cookie Test"); ?></label>
        </div><br />
        <span class="tip"><?php s18n_e("Uses a functional cookie that some bots won't store. (GDPR conform)"); ?></span>

        <div class="custom-control custom-checkbox reactions-control">
            <input id="secure-honeypot" type="checkbox" name="secure_honeypot" value="true" class="custom-control-input" <?php bt_checked($this->getValue("secure_honeypot")); ?> />
            <label for="secure-honeypot" class="custom-control-label mt-0"> <?php s18n_e("Honey-Pot Field"); ?></label>
        </div><br />
        <span class="tip"><?php s18n_e("Adds an invisible field that only bots will fill out."); ?></span>

        <div class="custom-control custom-checkbox reactions-control">
            <input id="secure-filter" type="checkbox" name="secure_filter" value="true" class="custom-control-input" <?php bt_checked($this->getValue("secure_filter")); ?> />
            <label for="secure-filter" class="custom-control-label mt-0"> <?php s18n_e("HTTP Referrer & User-Agent"); ?></label>
        </div><br />
        <span class="tip"><?php s18n_e("Filter Bots by validating the HTTP Referrer and User Agent values."); ?></span>
    </div>
</div>

<div class="form-group row mb-5">
    <label class="col-2 col-form-label"><?php s18n_e("Administration"); ?></label>
    <div class="col-10 pt-3">
        <div class="custom-control custom-checkbox reactions-control">
            <input id="content-view" type="checkbox" name="content_view" value="true" class="custom-control-input" <?php bt_checked($this->getValue("content_view")); ?> />
            <label for="content-view" class="custom-control-label mt-0"> <?php s18n_e("Show Ratings on Content List"); ?></label>
        </div><br />
        <span class="tip"><span class="text-danger"><?php s18n_e("EXPERIMENTAL - This feature may leads to unknown issues."); ?></span></span>
    </div>
</div>

<?php if(REACTIONS_PLUS) { ?>
    <div class="form-group row">
        <h2 class="align-top">Reactions <span class="plus">Plus</span></h2>
    </div>

    <div class="form-group row">
        <label for="log-ratings" class="col-2 col-form-label"><?php s18n_e("Log Reactions"); ?></label>
        <div class="col-10 pt-3">
            <div class="custom-control custom-checkbox reactions-control">
                <input id="log-ratings" type="checkbox" name="log_ratings" value="true" class="custom-control-input" <?php bt_checked($this->getValue("log_ratings")); ?> />
                <label for="log-ratings" class="custom-control-label mt-0"> <?php s18n_e("Log all ratings"); ?></label>
            </div><br />

            <div class="custom-control custom-checkbox reactions-control">
                <input id="log-changes" type="checkbox" name="log_changes" value="true" class="custom-control-input" <?php bt_checked($this->getValue("log_changes")); ?> />
                <label for="log-changes" class="custom-control-label mt-0"> <?php s18n_e("Log all changes"); ?></label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label for="store-cookie" class="col-2 col-form-label"><?php s18n_e("Cookie Storage"); ?></label>
        <div class="col-10 pt-3">
            <div class="custom-control custom-checkbox reactions-control">
                <input id="store-cookie" type="checkbox" name="store_cookie" value="true" class="custom-control-input" <?php bt_checked($this->getValue("store_cookie")); ?> />
                <label for="store-cookie" class="custom-control-label mt-0"> <?php s18n_e("Store ratings within a Cookie"); ?></label>
            </div><br />
            <span class="tip"><?php s18n_e("User Ratings will be stored as cookie on the client's computer."); ?></span>
        </div>
    </div>

    <div class="form-group row">
        <label for="store-server" class="col-2 col-form-label"><?php s18n_e("Hashed-IP Storage"); ?></label>
        <div class="col-10 pt-3">
            <div class="custom-control custom-checkbox reactions-control">
                <input id="store-server" type="checkbox" name="store_server" value="true" class="custom-control-input" <?php bt_checked($this->getValue("store_server")); ?> />
                <label for="store-server" class="custom-control-label mt-0"> <?php s18n_e("Store ratings on your server"); ?></label>
            </div><br />
            <span class="tip"><?php s18n_e("User Ratings will be stored with the hashed, anonymized IP address on your server."); ?></span>
        </div>
    </div>

    <div class="form-group row mb-5">
        <label for="grpd_cookie_key" class="col-2 col-form-label"><?php s18n_e("GDPR Requirement"); ?></label>
        <div class="col-10 pt-3">
            <div class="input-group" style="max-width: 500px;">
                <input type="text" class="form-control" name="gdpr_cookie_key" value="<?php echo $this->getValue("gdpr_cookie_key"); ?>" placeholder="<?php s18n_e("Cookie Key"); ?>" />
                <input type="text" class="form-control" name="gdpr_cookie_value" value="<?php echo $this->getValue("gdpr_cookie_value"); ?>" placeholder="<?php s18n_e("Cookie Value (Optional)"); ?>" />
            </div>
            <span class="tip"><?php s18n_e("Meta information, cookie & hashed IP storage only takes place when this cookie has been set."); ?></span>
            <span class="tip"><?php s18n_e("Leave the Cookie Key field empty to disable this option."); ?></span>
        </div>
    </div>
<?php } ?>
