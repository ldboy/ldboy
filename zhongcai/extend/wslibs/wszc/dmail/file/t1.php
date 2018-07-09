<div style="margin:0 auto; width:80%;">
    <p style="margin:0; padding:0; line-height:42px; font-size:24px; font-weight:500; text-align: center"><?php echo $title; ?></p>
    <p style="margin:0; padding:0; line-height:42px;"><?php echo $apply_name; ?></p>
    <?php if($bei_apply_name){ ?>
    <p style="margin:0; padding:0; line-height:42px; "><?php echo $bei_apply_name; ?></p>
    <?php } ?>
    <div style="margin:20px auto; padding:0; line-height:1.5;  text-indent:2em;"><?php echo $content; ?></div>
    <div style="margin:20px auto; padding-left:2%; line-height:24px; font-size:24px; font-weight:500;">文件列表如下:</div>
    <div style="margin:20px auto; padding-left:2%; line-height:24px; font-size:18px; font-weight:500;">
        <?php foreach ($files as $key => $value){ ?>
        <p><?php echo $value['name']; ?> <a href="<?php echo $value['url']; ?>">立即查看</a> </p>
        <?php } ?>

    </div>


    <div style="margin: 0 auto;width: 100%;">


        <div style="margin:35px auto 0; width:200px; float:right;font-size:22px;" >


            <div style="line-height:42px;"><span>石家庄仲裁委员会</span></div>
            <div style="line-height:42px;"><?php echo date('Y年m月d日',time()); ?></div>
        </div>

    </div>
</div>