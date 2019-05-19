<?php
/*
Copyright 2019 whatever127

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

$updateId = isset($_GET['id']) ? $_GET['id'] : 0;
$selectedLang = isset($_GET['pack']) ? $_GET['pack'] : 0;

require_once 'api/listlangs.php';
require_once 'api/listeditions.php';
require_once 'api/updateinfo.php';
require_once 'shared/style.php';

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
    die();
}

if(!checkUpdateIdValidity($updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

$updateInfo = uupUpdateInfo($updateId);
$updateInfo = isset($updateInfo['info']) ? $updateInfo['info'] : array();

$updateTitle = uupParseUpdateInfo($updateInfo, 'title');
if(isset($updateTitle['error'])) {
    $updateTitle = 'Unknown update: '.$updateId;
} else {
    $updateTitle = $updateTitle['info'];
}

$updateArch = uupParseUpdateInfo($updateInfo, 'arch');
if(isset($updateArch['error'])) {
    $updateArch = '';
} else {
    $updateArch = $updateArch['info'];
}

$updateTitle = $updateTitle.' '.$updateArch;

if($selectedLang) {
    if(isset($s['lang_'.strtolower($selectedLang)])) {
        $selectedLangName = $s['lang_'.strtolower($selectedLang)];
    } else {
        $langs = uupListLangs($updateId);
        $langs = $langs['langFancyNames'];

        $selectedLangName = $langs[strtolower($selectedLang)];
    }

    $editions = uupListEditions($selectedLang, $updateId);
    if(isset($editions['error'])) {
        fancyError($editions['error'], 'downloads');
        die();
    }
    $editions = $editions['editionFancyNames'];
    asort($editions);
} else {
    $editions = array();
    $selectedLangName = $s['allLangs'];
}

styleUpper('downloads', sprintf($s['selectEditionFor'], "$updateTitle, $selectedLangName"));
?>

<div class="ui horizontal divider">
    <h3><i class="archive icon"></i><?php echo $s['chooseEdition']; ?></h3>
</div>

<?php
if(!file_exists('packs/'.$updateId.'.json.gz')) {
    styleNoPackWarn();
}

if($updateArch == 'arm64') {
    styleCluelessUserArm64Warn();
}
?>

<div class="ui top attached segment">
    <form class="ui form" action="./download.php" method="get">
        <div class="field">
            <label><?php echo $s['update']; ?></label>
            <input type="text" disabled value="<?php echo $updateTitle; ?>">
            <input type="hidden" name="id" value="<?php echo $updateId; ?>">
        </div>

        <div class="field">
            <label><?php echo $s['lang']; ?></label>
            <input type="text" disabled value="<?php echo $selectedLangName; ?>">
            <input type="hidden" name="pack" value="<?php echo $selectedLang; ?>">
        </div>

        <div class="field">
            <label><?php echo $s['edition']; ?></label>
            <select class="ui search dropdown" name="edition">
                <option value="0"><?php echo $s['allEditions']; ?></option>
<?php
foreach($editions as $key => $val) {
    echo '<option value="'.$key.'">'.$val."</option>\n";
}
?>
            </select>
        </div>
        <button class="ui fluid right labeled icon primary button" type="submit">
            <i class="right arrow icon"></i>
            <?php echo $s['next']; ?>
        </button>
    </form>
</div>
<div class="ui bottom attached info message">
    <i class="info icon"></i>
    <?php echo $s['selectEditionInfoText']; ?>
</div>

<div class="ui fluid tiny three steps">
      <div class="completed step">
            <i class="world icon"></i>
            <div class="content">
                <div class="title"><?php echo $s['chooseLang']; ?></div>
                <div class="description"><?php echo $s['chooseLangDesc']; ?></div>
            </div>
      </div>

      <div class="active step">
            <i class="archive icon"></i>
            <div class="content">
                <div class="title"><?php echo $s['chooseEdition']; ?></div>
                <div class="description"><?php echo $s['chooseEditionDesc']; ?></div>
            </div>
      </div>

      <div class="step">
            <i class="briefcase icon"></i>
            <div class="content">
                <div class="title"><?php echo $s['summary']; ?></div>
                <div class="description"><?php echo $s['summaryDesc']; ?></div>
            </div>
      </div>
</div>

<script>$('select.dropdown').dropdown();</script>

<?php
styleLower();
?>
