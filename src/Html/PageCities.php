<?php
/**
 * @author Tahu Szilárd
 */
namespace App\Html;

use App\RestApiClient\Client;
use App\Html\Request;

class PageCities
{

    public static function render(array $counties, array $cities = [], ?int $selectedCountyId = null, ?string $filterLetter = null)
    {
        Request::handlePostRequest();

        echo '<h1>Városok</h1>';
        self::renderSearchCityBar();  

        self::renderForm($counties, $selectedCountyId);

        $filteredCities = Request::filterCitiesByCountyAndLetter($cities, $selectedCountyId, $filterLetter);

        if ($selectedCountyId) {
            self::renderABCSorting($cities, $selectedCountyId);
        }

        self::renderCityList($filteredCities, $selectedCountyId);
    }

    private static function renderSearchCityBar()
    {
        echo '
        <form method="post" action="">
            <input type="text" name="keyword" placeholder="Keresés város alapján" required />
            <button type="submit" name="btn-search-city" title="Keresés">
                <i class="fa fa-search"></i> Keresés
            </button>
        </form>
        <br>';
    }
    private static function renderForm(array $counties, ?int $selectedCountyId)
    {
        $options = self::generateCountyOptions($counties, $selectedCountyId);
        echo '
            <form method="post" action="">
                <label for="county-select">Megye választás:</label>
                <select id="county-select" name="selected-county" required>
                    <option value="">Megyék</option>
                    ' . $options . '
                </select>
                <button type="submit" name="btn-show-cities"><i class="fa fa-search"></i></button>
            </form>
        ';
    }

    private static function renderABCSorting(array $cities, ?int $selectedCountyId)
    {
        if ($selectedCountyId === null || empty($cities)) {
            return;
        }

        $filteredCities = Request::filterCitiesByCounty($cities, $selectedCountyId);
        $letters = [];
        foreach ($filteredCities as $city) {
            $initial = strtoupper(mb_substr($city['city'], 0, 1));
            if (!in_array($initial, $letters)) {
                $letters[] = $initial;
            }
        }

        sort($letters);

        echo '<div class="abc-sorting">';
        foreach ($letters as $letter) {
            echo "
                <form method='post' action='' style='display:inline;'>
                    <input type='hidden' name='selected-county' value='{$selectedCountyId}'>
                    <input type='hidden' name='filter-letter' value='{$letter}'>
                    <button type='submit' name='btn-filter-letter'>{$letter}</button>
                </form>";
        }
        echo '</div>';
    }

    private static function generateCountyOptions(array $counties, $selectedCountyId): string
    {
        $options = '';
        foreach ($counties as $county) {
            $selected = ($county['id'] == $selectedCountyId) ? 'selected' : '';
            $options .= "<option value=\"{$county['id']}\" {$selected}>{$county['name']}</option>";
        }
        return $options;
    }

    public static function renderCityList(array $cities, ?int $selectedCountyId)
    {
        if ($selectedCountyId === null) {
            return;
        }
    echo '
            <h3>Új város hozzáadása:</h3>
            <label for="county-select">Város hozzáadása:</label>
            <form method="post" action="">
                <input type="text" name="new_city-name" placeholder="Új város" required>
                <input type="text" name="new_zip-code" placeholder="Új zip kód" required>
                <input type="hidden" name="selected-county" value="' . $selectedCountyId . '" />
                <button type="submit" name="btn-save-new-city" title="Mentés">
                    <i class="fa fa-save"></i> Mentés
                </button>
            </form>
        ';

        if (empty($cities)) {
            echo '<p>Nincsenek városok ehhez a megyéhez.</p>';
            return;
        }

        echo '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Irányítószám</th>
                        <th>Város</th>
                        <th>Művelet</th>
                    </tr>
                </thead>
                <tbody>';

        $i = 0; 
        foreach ($cities as $city) {
            $rowClass = (++$i % 2 === 0) ? "even" : "odd"; 
            echo "
                <tr class='{$rowClass}'>
                    <td>{$city['id']}</td>
                    <td>{$city['zip_code']}</td>
                    <td>{$city['city']}</td>
                    <td class='flex'>
                        <form method='post' action='' class='inline-form'>
                            <input type='hidden' name='id' value='{$city['id']}'>
                            <button type='submit' name='btn-edit-city' value='{$city['id']}' title='Szerkesztés'>
                                <i class='fa fa-edit'></i>
                            </button>
                        </form>
                        <form method='post' action=''>
                            <button type='submit' name='btn-del-city' value='{$city['id']}' title='Törlés'>
                                <i class='fa fa-trash'></i>
                            </button>
                        </form>
                    </td>
                </tr>";
        }

        echo '</tbody></table>';
    }
    
    static function displayEditForm(array $city, array $counties)
    {
        echo "
        <h2>Város szerkesztése</h2>
        <form method='post' action=''>
            <input type='hidden' name='city_id' value='{$city['id']}' />
            <label for='city_name'>Város neve:</label>
            <input type='text' name='city_name' id='city_name' value='{$city['city']}' required />
            
            <label for='zip_code'>Irányítószám:</label>
            <input type='text' name='zip_code' id='zip_code' value='{$city['zip_code']}' required />
            
            <label for='county_id'>Megye:</label>
            <select name='county_id' id='county_id' required>";
            
        foreach ($counties as $county) {
            $selected = $county['id'] == $city['id_county'] ? 'selected' : '';
            echo "<option value='{$county['id']}' $selected>{$county['name']}</option>";
        }
    
        echo "
            </select>
            <button type='submit' name='btn-save-edit-city'>Mentés</button>
        </form>";
    } 
}
