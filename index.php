<?php
    if(isset($_POST['videos'])){
        die(json_encode($_POST['videos']));
    }

    $videos = [
        'yUajOaOwJJU',
        '_6tAK4tw0D0'
    ];
?>
<!DOCTYPE html>
<head>
    <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
    <title>Учет времени просмотра видео</title>
    <style>
        .videobox {border: 2px red solid; padding:10px; margin:15px; float:left; position: relative;}
        .percent {position: relative;  height: 20px; margin-bottom: 20px;}
        .percent div {background: green; width: 0; height: 20px; position: absolute; top:20px; left:0; color:white; font-weight: bold; text-align: center;}
    </style>
</head>
<html>
<body>
<? foreach($videos as $v):?>
    <div class='videobox'>
        <div id="<?=$v?>"></div>
        <p class="timer">Просмотрено секунд: <span>0</span></p>
        <div class="percent">Прогресс просмотра
            <div></div>
        </div>
    </div>
<? endforeach; ?>

<script>

    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    var players = {};
    var videos = {};
    var timers = {};
    var saveTime;

    function onYouTubeIframeAPIReady() {
        <? foreach($videos as $v):?>
        players['<?=$v?>'] = new YT.Player('<?=$v?>', {
            height: '360',
            width: '640',
            videoId: '<?=$v?>',
            playerVars: {
                enablejsapi: 1,
                controls: 2,
                modestbranding: 1,
                iv_load_policy: 3,
                rel: 0,
                showinfo: 1
            },
            events: {
                'onStateChange': StateChange('<?=$v?>')
            }
        });
        videos['<?=$v?>'] = 0;
        <? endforeach; ?>
    }

    function StateChange(ID) {
        return function (event) {
            var player = players[ID];
            if (event.data == YT.PlayerState.PLAYING) {

                saveTime = setInterval(function() {
                    $.post('index.php', {videos: videos}, '', 'json');
                }, 10000);

                timers[ID] = setInterval(function() {
                    videos[ID] += 1 * player.getPlaybackRate();
                    $('#'+ID).parent().find('.timer span').text(videos[ID]);
                    $('#'+ID).parent().find('.percent div').width( videos[ID] / parseInt(player.getDuration()) * 100 + '%').text(parseInt(videos[ID] / parseInt(player.getDuration()) * 100) + '%');
                }, 1000);
            }
            else {
                clearInterval(timers[ID]);
                if (event.data == YT.PlayerState.PAUSED || event.data == YT.PlayerState.ENDED) {
                    $.post('index.php', {videos: videos}, '', 'json');
                    clearInterval(saveTime);
                }
            }
        }
    }



</script>
</body>
</html>