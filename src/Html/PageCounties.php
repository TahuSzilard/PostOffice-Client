<?php
/**
 * @author Tahu Szilárd
 */
namespace App\Html;
 
use App\Html\AbstractPage;  
 
class PageCounties extends AbstractPage
{
    static function table(array $entities){
        echo '<h1>Megyék</h1>';
        self::searchBar();
        self::addNewCountyForm();
        echo'<table id="counties-table">';
        self::tableHead();
        self::tableBody($entities);
        echo "</table>";
    }
 
    static function tableHead()
    {
        echo '
        <thead>
            <tr>
                <th class="id-col" style="width: 10%;">ID</th>
                <th style="width: 70%;">Megnevezés</th>
                <th style="width: 20%; text-align: center;">Művelet</th>
            </tr>
        </thead>';
    }
    

    static function addNewCountyForm()
    {
        echo '
        <form method="post" action="" style="margin-bottom: 20px;">
            <label for="new_name">Új megye hozzáadása:</label>
            <input type="text" name="new_name" placeholder="Új név" required style="margin-left: 10px; padding: 5px;">
            <button type="submit" name="btn-save-new-county" title="Mentés" style="padding: 5px 10px; margin-left: 10px;">
                <i class="fa fa-save"></i> Mentés
            </button>
        </form>
        ';
    }

    static function editor()
    {
        echo '
            <th>&nbsp;</th>
            <th>
                <form name="county-editor" method="post" action="">
                    <input type="hidden" id="id" name="id">
                    <input type="search" id="name" name="name" placeholder="Megye" required>
                    <button type="submit" id="btn-update-county" name="btn-update-county" title="Ment">Frissítés</button> <!-- új gomb -->
                    <button type="button" id="btn-cancel-county" title="Mégse">Mégse</button>
                </form>
            </th>
            <th class="flex">
            &nbsp;
            </th>
        ';
    }
    static function tableBody(array $entities)
    {
        echo '<tbody>';
        $i = 0;
        foreach ($entities as $entity) {
            echo "
            <tr class='" . (++$i % 2 ? "odd" : "even") . "'>
                <td style='width: 10%; text-align: center;'>{$entity['id']}</td>
                <td style='width: 70%;'>{$entity['name']}</td>
                <td style='width: 20%; text-align: center;'>
                    <form method='post' action='' class='inline-form'>
                        <input type='hidden' name='id' value='{$entity['id']}'>
                        <button type='submit' name='btn-edit-county' value='{$entity['id']}' title='Szerkesztés'>
                            <i class='fa fa-edit'></i>
                        </button>
                    </form>
                    <form method='post' action=''>
                        <button type='submit' id='btn-del-county-{$entity['id']}' name='btn-del-county' value='{$entity['id']}' title='Töröl'>
                            <i class='fa fa-trash'></i>
                        </button>
                    </form>
                </td>
            </tr>";
        }
        echo '</tbody>';
    }
    
    public static function displayEditForm($countyResponse)
    {
        if (isset($countyResponse['data'])) {
            $county = $countyResponse['data'];
            $id = $county['id'];
            $name = $county['name'];
    
            echo "
            <form method='post' action=''>
                <input type='hidden' name='id' value='{$id}'>
                <label for='edit_name'>Új név:</label>
                <input type='text' name='edit_name' value='{$name}' required>
                <button type='submit' name='btn-save-edit-county'>Mentés</button>
                <button type='submit' name='btn-home'>Kilépés</button>
            </form>
            ";
        } else {
            echo "<p>Hiba történt a megye adatok lekérésekor.</p>";
        }
    }
}