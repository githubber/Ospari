<?php
$lang = $this->lang;
$nzmap = $this->nzmap;
if (!$nzmap) {
    $nzmap = new NZ_Map();
}
?>
<html>
    <head>
        <title><?php echo SITE_NAME; ?></title>
    </head>
    <body bgcolor="#EBEBEB" style="margin: 0px;">
    <center>
        <div class="container" style="max-width: 670px; width: 85%;">
            <table cellspacing="0" cellpadding="0" border="0" width="100%" style="border-collapse:collapse;">
                <tbody>
                    <tr>
                        <td style="padding:20px 0px 10px 10px;text-align:left">
                            <a href="<?php echo SITE_URL; ?>" style="color:#006699;text-decoration:none" target="_blank"><img height="41" width="200" src="http://www.ranksider.com/img/ranksider_logo_w200.png" alt="ranksider logo" title="ranksider logo" style="text-decoration:none; border:none"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="background-color: #DDDDDD; padding: 2px;">
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="content-outer" style="border-collapse:collapse;table-layout:fixed">
                    <tbody>
                        <tr>
                            <td class="content" style="background-color:#ffffff; border:none; color:#353c4a; font-family:Calibri; font-size:14px; line-height:1.4em; max-width:628px; padding-top:40px; padding-right:30px; padding-bottom:12px; padding-left:30px; text-align:left;">
                                <h1 style="font-size:24px;letter-spacing:-2px;line-height:40px;color:#3D74A5;font-family:Calibri; margin:0"><?php echo $this->subject; ?></h1>
                                <hr style="height:1px; border:0; background-color: #3D74A5;">
                                <p>
                                    <?php
                                    if (is_object($this->user)) {
                                        //echo $this->user->getGreetingText();
                                    }
                                    ?>
                                <?php 
                                
                                if( $this->body ){
                                    echo $this->body;
                                }else{
                                    echo $this->text;
                                }                                                                      
                                
                                ?>
                                </p>

                               


                                <p><?php echo $lang->get('mail_signature'); ?></p>
                                <hr style="height:1px; border:0; background-color: #3D74A5;">
                                <?php
                                if ($unsubscribe_html = $nzmap->unsubscribe_html) {
                                    echo $unsubscribe_html;
                                } else if ($nzmap->unsubscribe) {
                                    echo '<p style="text-align:right"><a href="https://www.ranksider.com/my/account/notifications/">' . $lang->unsubscribe . '</a></p>';
                                }
                                
                                ?>
                                <p><center><span style="color: #989898; font-size: 12px; line-height: 14px;"><?php echo $lang->get('mail_footer_new'); ?></span></center></p>
                    </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </center>
</body>
</html>