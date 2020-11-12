<?php

#
# Ceres
# (c)2020 Simon Strudwick
# 

    include 'ceres/Ceres.php';
    
    $pageName = getParameter('page') ?: 'page';    
    $entryName = getParameter('entry') ?: 'home';

    $page = new Page($entryName, $pageName);
    if (!$page->hasContent()) {
        $page = new Page(HOME_PAGE_NAME, "page");
    }
    
    // $showPageTitle = !in_array($page->pageName, HIDE_TITLE_ON_PAGES);
    
    $headerContent = new Page("header", "fragment");
    $navigationContent = new Page("navigation", "fragment");
    $footerContent = new Page("footer", "fragment");
?>

<html>
    <head>
        <title><?php echo SITE_TITLE ?> - <?php echo $page->title ?></title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <link rel="stylesheet" type="text/css" href="/main.css">
    </head>
    <body>
        <div id="header">
            <div id="header_title">
                <?php if ($headerContent->hasContent()) { echo $headerContent->html(false); } ?>
            </div>
            <div id="nav">
                <?php if ($navigationContent->hasContent()) { echo $navigationContent->html(false); } ?>
            </div>
        </div>
        
        <div id="content">
            <div class="articleHeader">
                <?php echo $page->headerHTML(); ?>
            </div>        
            
            <div class="articleContent">
                <?php echo $page->html(); ?>
            </div>
            
            <div class="articleFooter">
                <?php echo $page->footerHTML(); ?>
            </div>
        </div>
        
        <div id="footer">
            <?php echo $footerContent->html(false); ?>
        </div>
    </body>
</html>
