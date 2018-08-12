{__NOLAYOUT__}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        *{
            margin: 0;
            padding:0;
        }
        html{
            width:100%;
            height:100%;
        }
        body{
            /*background: #eeeeee;*/
            width:100%;
            height:100%;
        }
        .wrap{
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: center;
            padding-top: 10%;
            box-sizing: border-box;
            width:100%;
            height:100%;
        }
        .box{
            width:100%;
        }
        .box p{
            text-align: center;
            line-height: 30px;
            color:#565857;
            font-size: 18px;
        }
        .box p a{
            text-decoration: none;
            color:#fd8a0b;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="box">
        <?php switch ($code) {?>
            <?php case 1:?>
        <p style="margin-bottom: 40px;">
        <img src="/public/static/common/img/successful.png" width="190"/>
        </p>

        <p style="font-size: 30px; color:#565857;"><?php echo(strip_tags($msg));?></p>
            <?php break;?>
            <?php case 0:?>
        <p>
        <img src="/public/static/common/img/error.png" height="311" width="242"/>
        </p>

        <p style="font-size: 30px; color:#565857;"><?php echo(strip_tags($msg));?></p>
            <?php break;?>
        <?php } ?>
        <p style="margin-top:15px;">页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b></p>
    </div>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>