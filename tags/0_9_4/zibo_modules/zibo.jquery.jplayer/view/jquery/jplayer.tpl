<div id="{$playerId}"></div>

<div id="{$playerId}Container" class="jplayerContainer">
    <ul id="{$playerId}-icons" class="ui-widget ui-helper-clearfix icons">
        <li id="{$playerId}-play" class="ui-state-default ui-corner-all play"><span class="ui-icon ui-icon-play"></span></li>
        <li id="{$playerId}-pause" class="ui-state-default ui-corner-all pause"><span class="ui-icon ui-icon-pause"></span></li>
        <li id="{$playerId}-stop" class="ui-state-default ui-corner-all stop"><span class="ui-icon ui-icon-stop"></span></li>
        <li id="{$playerId}-volume-min" class="ui-state-default ui-corner-all volume-min"><span class="ui-icon ui-icon-volume-off"></span></li>
        <li id="{$playerId}-volume-max" class="ui-state-default ui-corner-all volume-max"><span class="ui-icon ui-icon-volume-on"></span></li>
    </ul>

    <!-- Sliders -->
    <div id="{$playerId}-slider-volume" class="volume"></div>
    <div id="{$playerId}-playback" class="playbackContainer">
        <div id="{$playerId}-slider-playback"></div>
        <div id="{$playerId}-loader-playback"></div>
    </div>
</div>
