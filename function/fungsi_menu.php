<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
error_reporting(error_reporting() & ~E_NOTICE);
require_once('./config.php');
require_once('./class/c_user.php');

function menu() {
    global $dbLink;
    $filter="";
    if (!empty($_GET['page'])) {
        $db_menu = "SELECT * FROM aki_menu WHERE link = '".$_GET['page']."'";
        $d_menu  = mysql_query($db_menu, $dbLink);
        $d_m     = mysql_fetch_assoc($d_menu);
        $page    = substr($d_m['link'],5);
        $k_page  = substr($d_m['kodeMenu'],0,2);
    } else {
        $page   = '';
        $k_page = '';
    }

    $privilege = secureParam($_SESSION["my"]->privilege, $dbLink);
    if ($privilege != "GODMODE")
        $filter = "AND gp.kodeMenu='" . $privilege . "'";

    $q = "SELECT DISTINCT m.kodeMenu, m.judul, m.link FROM aki_menu m  INNER JOIN aki_groupprivilege gp ON m.kodeMenu=gp.kodeGroup WHERE m.aktif='Y' " . $filter . "  AND 
              m.kodeMenu IN (" . $_SESSION["my"]->menus . ") ORDER BY m.kodeMenu;";
    $cari_menu = mysql_query($q, $dbLink);
    ?>

        <li class="<?php if (empty($k_page)) { echo "active"; } ?>">
            <a href="index.php">
                <i class="fa fa-dashboard"></i><span>Home</span>
                <span class="pull-right-container"></span>
            </a>
        </li>
        
        <?php
        $currentLevel = 0;
        while ($menu = mysql_fetch_array($cari_menu)) {
            if ($menu['kodeMenu'] == '99') {
                $fa99 = 1;$fa=99;
            }
            if ($menu['kodeMenu'] == '10') {
                $fa10 = 1;$fa=10;
            }
            if ($menu['kodeMenu'] == '20') {
                $fa20 = 1;$fa=20;
            }
            if ($menu['kodeMenu'] == '30') {
                $fa30 = 1;$fa=30;
            }
            $tempArr = explode(".", $menu['kodeMenu']);
            if (strlen($menu['link']) == 0) {
                $tempLink = '';
            } else {
                $tempLink = "index.php?page=" . $menu['link'];
            }
            if (count($tempArr) > $currentLevel) {
                if (count($tempArr) == 1) {
                    if ($fa10){ ?>
                            <li class="treeview <?php if ($k_page == $menu['kodeMenu']) { echo "active"; } ?>"><a href="<?= $tempLink; ?>"><i class="fa fa-edit"></i><span><?= $menu['judul'] ?>
                                </span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></a>
                    <?php }else{ ?>
                        <li class="<?php if ($k_page == $menu['kodeMenu']) { echo "active"; } ?>"><a href="<?= $tempLink; ?>"><i class="fa fa-share"></i><?= $menu['judul']; ?></a></li>
                    <?php }
                } elseif (count($tempArr) == 2) { ?>
                    <ul class="treeview-menu <?php if ($k_page == $menu['kodeMenu']) { echo "active"; } ?>">
                        <li class="<?php $list_page = substr($menu['link'],5); if ($page == $list_page) { echo "active"; } ?>"><a href="<?= $tempLink; ?>"><i class="fa fa-share"></i><?= $menu['judul']; ?></a></li>
                <?php }
            } elseif (count($tempArr) == $currentLevel) {
                if (count($tempArr) == 1) { ?>
                    </li>
                    <li class="<?php $list_page = substr($menu['link'],5); if ($page == $list_page) { echo "active"; } ?>"><a href="<?= $tempLink ?>"><i class="fa fa-share"></i><?= $menu['judul']; ?></a></li>
                <?php } else { ?>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    <li class="<?php $list_page = substr($menu['link'],5); if ($page == $list_page) { echo "active"; } ?>"><a href="<?= $tempLink; ?>"><i class="fa fa-share"></i><?= $menu['judul']; ?></a></li>
                <?php }
            } elseif (count($tempArr) < $currentLevel) { ?>
                </ul></li>
                <li class="treeview <?php if ($k_page == $menu['kodeMenu']) { echo "active"; } ?>"><a href="<?= $tempLink; ?>">
                <?php
                if ($fa=='99'){
                    echo '<i class="fa fa-gears"></i>';
                }elseif ($fa=='20'){
                    echo '<i class="fa fa-laptop"></i>';
                }elseif ($fa=='30'){
                    echo '<i class="fa fa-files-o"></i>';
                }
                ?>
                <span><?= $menu['judul']; ?></span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
            <?php }
            $currentLevel = count($tempArr);
        }
        if ($currentLevel == 1) {
            echo '</li>';
        } elseif ($currentLevel == 2) {
            echo '</ul></li>';
        }
        ?>
        <li><a href="logout.php?page=login_detail&eventCode=20"><i class="fa fa-sign-out"></i><span>Log Out</span></a></li>


<?php } //function menu ?>

