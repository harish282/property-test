<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate, max-age=0" />
    <!--META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 1 Oct 2014 11:12:01 GMT">
    <meta http-equiv="Last-Modified" content="Sun, 5 Aug 2013 14:59:42 GMT"> 
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="public"-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
    <meta name="google-site-verification" content="DDoAoHTv4OOMDGbzlO82rUAX_tmuX9B7lciyt-ld9k0" />
    <meta name="viewport" content="width=screen-width">

    <meta name="expires" content="<?=gmdate('D, d M Y H:i:s \G\M\T', time() - 14*24*3600)?>">
    
    <link rel="stylesheet" type="text/css" href="" />
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link rel='stylesheet' type='text/css' href='<?php echo url_site('/assets/bootstrap/bootstrap.min.css');?>'>
	<?php for($i=0, $count = count($view->_css); $i<$count;$i++):?>
	<link rel='stylesheet' type='text/css' href='<?php echo url_site($view->_css[$i]);?>'>
    <?php endfor;?>
</head>
<body class="">
<div id="wrap">
    <div id="header"><!--header start here-->
        <?php //view('menu')->render(); ?>
    </div>
    <!--header ends here-->
    <?php 
        $notification = \SohamGreens\SimpleMvc\Notification::get();
        if (!empty($notification['message']))
        {
            ?>
            <div id="errorwin" class="msg <?php echo $notification['type'] == SohamGreens\SimpleMvc\Notification::INFO? 'success':'failure'?>">
                        <span><?php echo $notification['message'];?></span>
            </div>                   
            <?php
            
            echo '<script>window.setTimeout(function(){$("#errorwin").hide("slow");},10000)</script>';
            ///echo '<script>showMsg("'.$notification['message'].'","'.($notification['type'] == Notification::INFO? 'info':'error').'")</script>';
        }else{
            ?>
            <div id="errorwin" class="msg success" style="display: none;">
                <span></span>
            </div>
            <?php
        }
    ?>
    <div id="" style="padding: 2rem;"><!--wid_img_wrap start here-->
        
        <?php require_once $view->viewFile;?>
        </div><!--wid_img_wrap ends here-->

     <div class="clear"></div>
    <div id="footer"><!--footer start here-->

    </div><!--footer ends here-->
     
</div>
<script src="<?php echo url_site('/assets/bootstrap/bootstrap.min.js');?>"></script>

</body></html>