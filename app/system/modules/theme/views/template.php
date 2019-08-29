<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $intl->getLocaleTag()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <?php $view->style('theme', 'system/theme:css/theme.css') ?>
        <?php $view->script('theme', 'system/theme:js/theme.js', ['vue']) ?>
        <?= $view->render('head') ?>
    </head>
    <body class="<?= $pageClass ?>">

        <div class="tm-sidebar-left uk-visible@s" :style="sbStyle" v-cloak>

            <div class="uk-position-relative uk-height-1-1" uk-overflow-auto>
                <theme-side-menu></theme-side-menu>
                <ul class="tm-sidebar-menu uk-nav-default uk-nav-parent-icon" uk-nav data-url="<?= $view->url('@system/adminmenu') ?>" uk-sortable="cls-custom: tm-sortable-dragged; handle: .tm-menu-item">
                    <li :class="['', { 'uk-active': item.active }]" v-for="item in nav" :data-id="item.id">
                        <a class="tm-menu-item" :href="item.url">
                            <span class="tm-menu-image"><img :alt="item.label | trans" :src="item.icon"></span>
                            <span class="tm-menu-text uk-text-middle" v-if="sb">{{ item.label | trans }}</span>
                        </a>
                        <div class="tm-dropdown-parent-items uk-position-fixed" uk-dropdown="pos: right-top; delayHide: 0; offset: 0;" v-if="showDropdown(item)">
                            <ul class="uk-nav uk-dropdown-nav">
                                <li class="uk-nav-header"><a :href="item.url">{{ item.label | trans }}</a></li>
                                <li :class="{ 'uk-active': child.active }" v-for="child in getChildren(item)" class="uk-margin-remove" style="">
                                    <a :href="child.url">{{ child.label | trans }}</a>
                                </li>
                            </ul>
                        </div>
                        <ul class="uk-nav tm-parent-items" v-if="isActiveParent(item) && sb">
                            <li :class="{ 'uk-active': child.active }" v-for="child in getChildren(item)" class="uk-margin-remove" style="">
                                <a :href="child.url"><span>{{ child.label | trans }}</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

        </div>

        <div class="tm-page">

            <div uk-sticky="media: 960; show-on-up: true; animation: uk-animation-slide-top; cls-active: uk-navbar-sticky; sel-target: .uk-navbar-container;">
                <div class="tm-navbar-container uk-navbar-container" v-cloak>
                    <div class="uk-container uk-container-expand">

                        <nav uk-navbar='{"align":"left","boundary":"!.uk-navbar","dropbar":true,"dropbar-anchor":"!.uk-navbar","dropbar-mode":"slide"}'>
                            <div class="uk-navbar-left">
                                <ul class="uk-navbar-nav uk-visible@m">
                                    <li>
                                        <a class="uk-link-heading" :href="item.url">
                                            <h1 class="uk-logo uk-margin-remove">{{ item.label | trans }}</h1>
                                        </a>
                                    </li>
                                    <li class="uk-parent" v-if="objLength(subnav)">
                                        <a>
                                            <h2 class="uk-h4 uk-margin-remove">
                                                <span>{{ getActive(subnav) | trans}}</span>
                                                <span uk-icon="chevron-down" class="uk-icon"></span>
                                            </h2>
                                        </a>
                                        <div class="uk-navbar-dropdown uk-navbar-dropdown-dropbar">
                                            <div class="uk-navbar-dropdown-grid uk-child-width-1-1 uk-grid uk-grid-stack" uk-grid>
                                                <div>
                                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                                        <li :class="{ 'uk-active': item.active }" v-for="item in subnav">
                                                            <a class="uk-h5 uk-margin-remove" :href="item.url">{{ item.label | trans }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <theme-breadcrumbs></theme-breadcrumbs>
                                <theme-navbar-items dir="left"></theme-navbar-items>
                                <div class="uk-navbar-item uk-hidden@m">
                                    <span class="uk-logo uk-margin-remove">{{ item.label | trans }}</span>
                                </div>
                            </div>

                            <div class="uk-navbar-right">
                                <div class="uk-navbar-item uk-visible@m">
                                    <theme-navbar-items dir="right"></theme-navbar-items>
                                    <ul class="uk-iconnav uk-margin-right">
                                        <li><a class="tm-icon-help" href="https://discord.gg/e7Kw47E" :title="'Get Help' | trans " target="_blank"></a></li>
                                        <li><a class="tm-icon-visit" :href="$url.route('')" :title="'Visit Site' | trans " target="_blank"></a></li>
                                        <li><a class="tm-icon-logout" href="<?= $view->url('@user/logout', ['redirect' => 'admin/login']) ?>" :title="'Logout' | trans "></a></li>
                                    </ul>
                                    <a class="uk-link-muted" :href="$url.route('admin/user/edit', {id: user.id})" :title="'Profile' | trans ">
                                        <img class="uk-border-circle" height="32" width="32" :title="user.name" v-gravatar="user.email" />
                                        <span class="uk-text-small uk-text-middle" v-text="user.username"></span>
                                    </a>
                                </div>
                                <a href="#offcanvas" class="uk-navbar-item uk-hidden@m" uk-navbar-toggle-icon uk-toggle="target: #offcanvas"></a>
                            </div>
                        </nav>
                        <theme-top-menu></theme-top-menu>

                        <div id="offcanvas" uk-offcanvas="mode: push; overlay: true" class="tm-offcanvas uk-hidden@m">
                            <div class="uk-offcanvas-bar">

                                <button class="uk-offcanvas-close uk-close uk-padding-small uk-padding-remove-horizontal" ratio="1.2" type="button" uk-close=""></button>

                                <h4 v-show="subnav" class="uk-h4 uk-margin-small">{{ item.label | trans }}</h4>
                                <ul class="uk-nav uk-nav-default">
                                    <li :class="{ 'uk-active': item.active }" v-for="item in subnav">
                                        <a :href="item.url">{{ item.label | trans }}</a>
                                    </li>
                                </ul>
                                <hr class="uk-margin" v-show="subnav">
                                <h4 class="uk-h4 uk-margin-small">{{ 'Extensions' | trans }}</h4>
                                <ul class="uk-nav uk-nav-default">
                                    <li :class="{ 'uk-active': item.active }" v-for="item in nav">
                                        <a :href="item.url">
                                            <img class="uk-margin-small-right" width="27" height="27" :alt="item.label | trans" :src="item.icon">
                                            <span class="uk-text-middle">{{ item.label | trans }}</span>
                                        </a>
                                    </li>
                                </ul>

                                <hr class="uk-margin">

                                <ul class="uk-nav uk-nav-default">
                                    <li>
                                        <a class="uk-link-muted" :href="$url.route('admin/user/edit', {id: user.id})" :title="'Profile' | trans ">
                                            <img class="uk-border-circle uk-margin-small-right" height="32" width="32" :title="user.name" v-gravatar="user.email" />
                                            <span class="uk-text-small uk-text-middle" v-text="user.username"></span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="uk-nav uk-nav-default uk-margin-small-top uk-text-small">
                                    <li><a :href="$url.route('')" target="_blank"><span class="uk-margin-small-right" uk-icon="home"></span><span class="uk-text-middle">{{ 'Visit Site' | trans }}</span></a></li>
                                    <li><a href="<?= $view->url('@user/logout', ['redirect' => 'admin/login']) ?>"><span class="uk-margin-small-right" uk-icon="sign-out"></span><span class="uk-text-middle">{{ 'Logout' | trans }}</span></a></li>
                                </ul>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tm-main uk-section uk-section-default">
                <div class="uk-container">
                    <div class="tm-content" uk-height-viewport="expand: true">
                        <?= $view->render('content') ?>
                    </div>
                </div>
            </div>

            <div class="uk-hidden"><?= $view->render('messages') ?></div>

        </div>

    </body>
</html>
