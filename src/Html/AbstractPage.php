<?php
 /**
 * @author Tahu Szilárd
 */
namespace App\Html;
 
use App\Interfaces\PageInterface;
 
abstract class AbstractPage implements PageInterface
{

    static function head()
    {
        echo '<!DOCTYPE html>
        <html lang="hu-hu">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <link rel="stylesheet" href="src/CSS/city.css">
            <title>REST API Ügyfél</title>
           
            <!-- Script -->
            <script src="js/jquery-3.7.1.js" type="text/javascript"></script>
            <script src="js/app.js" type="text/javascript"></script>
        
        </head>';
    }

    static function nav()
    {
        echo '
        <nav>
            <form name = "nav" method = "post" action = "index.php">
                <button type = "submit" name = "btn-home">
                    <i class = "fa fa-home" title = "Kezdőlap"></i>
                </button>
                <button type = "submit" name = "btn-counties">Megyék</button>
                <button type = "submit" name = "btn-cities">Városok</button>
            </form>
        </nav>';
    }

    static function footer()
    {
        echo '
        <footer>
            Készítette: Tahu Szilárd
        </footer>
        </html>';
    }
 
    abstract static function tableHead();
 
    abstract static function tableBody(array $entities);
 
    abstract static function table(array $entities);
 
    abstract static function editor();

    static function searchbar()
    {
        echo '
            <form method="post" action="">
                <input type="text" name="keyword" placeholder="Keresés város vagy irányítószám alapján" required />
                <button type="submit" name="btn-search-city" title="Keresés">
                    <i class="fa fa-search"></i> Keresés
                </button>
            </form>
            <br>';
    }
 
    static function displaySearchResults($results, $keyword)
    {
        if (!empty($results)) {
            echo "<table><thead><tr><th>Index</th><th>Név</th><th>Műveletek</th></tr></thead><tbody>";
                
            foreach ($results as $result) {
                echo "
                    <tr>
                        <td>{$result['id']}</td>
                        <td>{$result['name']}</td>
                        <td class='flex'>
                            <form method='post' action='' class='inline-form'>
                                <button type='submit' name='btn-edit-county' value='{$result['id']}' title='Szerkesztés'>
                                    <i class='fa fa-edit'></i>
                                </button>
                            </form>
                            <form method='post' action=''>
                                <button type='submit' name='btn-del-county' value='{$result['id']}' title='Töröl'>
                                    <i class='fa fa-trash'></i>
                                </button>
                            </form>
                        </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Nincs találat a következő keresési kifejezésre: <strong>$keyword</strong></p>";
        }
    }  
    static function displayCitySearchResults(array $filteredCities, array $counties)
    {
        echo '<h2>Keresési eredmények:</h2>';
        if (!empty($filteredCities)) {
            echo '<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Város</th>
                            <th>Irányítószám</th>
                            <th>Megye</th>
                            <th>Műveletek</th>

                        </tr>
                    </thead>
                    <tbody>';
            foreach ($filteredCities as $city) {
                $countyName = '';
                foreach ($counties as $county) {
                    if ($county['id'] == $city['id_county']) {
                        $countyName = $county['name'];
                        break;
                    }
                }
                $i = 0; 
                foreach ($filteredCities as $city) {
                    $rowClass = (++$i % 2 === 0) ? "even" : "odd"; 
                    echo "
                        <tr class='{$rowClass}'>
                            <td>{$city['id']}</td>
                            <td>{$city['city']}</td>
                            <td>{$city['zip_code']}</td>
                            <td>{$countyName}</td>
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
            }
            echo '</tbody></table>';
        } else {
            echo '<p>Nincs találat a megadott keresési feltétellel.</p>';
        }
    }
    
}

