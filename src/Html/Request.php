<?php
/**
 * @author Tahu Szilárd
 */
namespace App\Html;

use App\RestApiClient\Client;
use App\Interfaces\PageInterface;
use App\Html\AbstractPage;

class Request {

    static function handle()
    {
        switch ($_SERVER["REQUEST_METHOD"]){
            case "POST":
                self::postRequest();
                break;

        }
    }

    private static function postRequest()
    {
        $request = $_REQUEST;

        switch ($request) {
            case isset($request['btn-home']):
                break;

            case isset($request['btn-counties']):
                PageCounties::table(self::getCounties());
                break;

            case isset($request['btn-save-county']):
                $client = new Client();
                if (!empty($request['id'])) {
                    $data['id'] = $request['id'];
                }
                break;

            case isset($request['btn-del-county']):
                $id = $request['btn-del-county'];
                $client = new Client();
                $response = $client->delete('counties/' . $id, $id);
                if ($response && isset($response['success']) && $response['success']) {
                    echo "Sikeres törlés!";
                } 
                PageCounties::table(self::getCounties());
                break;

            case isset($request['btn-save-new-county']):
                $name = $request['new_name'];
                $client = new Client();

                $existingCounties = $client->get('counties');
                if ($existingCounties && isset($existingCounties['data'])) {
                    foreach ($existingCounties['data'] as $county) {
                        if (strcasecmp($county['name'], $name) === 0) {
                            //echo "Ez a megye már létezik!";
                            return;
                        }
                    }
                }

                $response = $client->post('counties', ['name' => $name]);
                if ($response && isset($response['success']) && $response['success']) {
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit; 
                } else {
                    echo "Hiba történt a mentés során!";
                }
                PageCounties::table(self::getCounties());
                break;

            case isset($request['btn-search']):
                $keyword = $_POST['keyword'];
                $results = self::searchCountiesByName($keyword);
                echo "<h2>Keresési eredmények:</h2>";
                AbstractPage::searchbar(); 
                AbstractPage::displaySearchResults($results, $keyword);
                break;

            case isset($request['btn-edit-county']):
                $id = $request['btn-edit-county'];
                $client = new Client();
                $county = $client->get('counties/' . $id); 

                PageCounties::displayEditForm($county); 
                break;

            case isset($request['btn-save-edit-county']):
                $id = $request['id'];
                $newName = $request['edit_name'];
                $client = new Client();

                $response = $client->put('counties/' . $id, ['name' => $newName]);
                if ($response && isset($response['success']) && $response['success']) {
                    echo "A név sikeresen módosítva lett!";
                } else {
                    echo "Hiba történt a módosítás során!";
                }
                PageCounties::table(self::getCounties());
                break;

            case isset($request['btn-show-cities']):
                self::handleShowCities($request);
                break;

            case isset($request['btn-filter-letter']):
                self::handleFilterLetter($request);
                break;

            case isset($request['btn-save-new-city']):
                self::handleNewCityRequest($request);
                break;

            case isset($request['btn-del-city']):
                self::handleCityDeletion($request);
                break;

            case isset($request['btn-search-city']):
                self::handleCitySearch($request['keyword']);
                break;

            case isset($request['btn-edit-city']):
                self::handleEditCity($request);
                break;

            case isset($request['btn-save-edit-city']):
                self::handleSaveEditCity($request);
                break;

            default:
                $counties = self::getCounties();
                PageCities::render($counties);
                break;
        }
    }

    private static function getCounties(): array
    {
        $client = new Client();
        $response = $client->get('counties');

        return $response['data'];
    }

    static function searchCountiesByName($keyword)
    {
        $client = new Client();
        $counties = $client->get('counties');

        $results = [];
        foreach ($counties['data'] as $county) {
            if (stripos($county['name'], $keyword) !== false) {
                $results[] = $county;
            }
        }

        return $results;
    }

    private static function getCities(): array
    {
        $client = new Client();
        $response = $client->get('cities');

        return $response['data'] ?? [];
    }

    public static function filterCitiesByCounty(array $cities, ?int $countyId): array
    {
        if ($countyId === null) {
            return [];
        }

        return array_filter($cities, function ($city) use ($countyId) {
            return intval($city['id_county']) === $countyId;
        });
    }

    public static function filterCitiesByCountyAndLetter(array $cities, ?int $countyId, ?string $letter): array
    {
        if ($countyId === null) {
            return [];
        }

        $filteredCities = array_filter($cities, function ($city) use ($countyId) {
            return intval($city['id_county']) === $countyId;
        });

        if ($letter) {
            $filteredCities = array_filter($filteredCities, function ($city) use ($letter) {
                return stripos($city['city'], $letter) === 0;
            });
        }

        return $filteredCities;
    }

    public static function handlePostRequest()
    {
        if (isset($_POST['btn-save-new-city'])) {
            $selectedCountyId = $_POST['selected-county'] ?? null;
            $newCityName = $_POST['new_city-name'] ?? '';
            $newZipCode = $_POST['new_zip-code'] ?? '';
            if ($selectedCountyId && !empty($newCityName) && !empty($newZipCode)) {
                $client = new Client();

                // Ellenőrizzük, hogy a város már létezik-e
                $existingCities = $client->get('cities');
                if ($existingCities && isset($existingCities['data'])) {
                    foreach ($existingCities['data'] as $city) {
                        if (strcasecmp($city['city'], $newCityName) === 0 && $city['zip_code'] === $newZipCode && intval($city['id_county']) === intval($selectedCountyId)) {
                            echo "Ez a város már létezik!";
                            return;
                        }
                    }
                }

                $response = $client->post('cities', [
                    'name' => $newCityName,
                    'zip_code' => $newZipCode,
                    'county_id' => $selectedCountyId
                ]);

                if ($response && isset($response['success']) && $response['success']) {
                    echo "A város sikeresen hozzáadva!";
                }
            } else {
                echo "Kérlek válassz megyét és adj meg városnevet és irányítószámot!";
            }
        }
    }

    private static function handleNewCityRequest($request)
    {
        $newCityName = $request['new_city-name'] ?? null;
        $newZipCode = $request['new_zip-code'] ?? null;
        $selectedCountyId = $request['selected-county'] ?? null;

        if ($newCityName && $newZipCode && $selectedCountyId) {
            $response = self::addCity($newCityName, $newZipCode, $selectedCountyId);

            if ($response && isset($response['success']) && $response['success']) {
                echo "Új város hozzáadva!";
            } 
        } else {
            echo "Kérem, töltse ki az összes mezőt!";
        }
        $counties = self::getCounties();
        $cities = self::getCities();
        PageCities::render($counties, $cities, $selectedCountyId);
    }

    private static function addCity($newCityName, $newZipCode, $selectedCountyId)
    {
        $client = new Client();

        // Az összes város adatának lekérése
        $existingCities = $client->get('cities');

        if ($existingCities && isset($existingCities['data'])) {
            foreach ($existingCities['data'] as $city) {
                if (
                    strcasecmp($city['city'], $newCityName) === 0 &&
                    $city['zip_code'] === $newZipCode &&
                    intval($city['id_county']) === intval($selectedCountyId)
                ) {
                    echo "Ez a város már létezik!";
                    return false;
                }
            }
        }

        // Új város hozzáadása, ha nem találtunk duplikációt
        return $client->post('cities', [
            'id_county' => $selectedCountyId,
            'city' => $newCityName,
            'zip_code' => $newZipCode
        ]);
    }


    private static function handleCityDeletion($request)
    {
        $id = $request['btn-del-city'];
        $client = new Client();
        $response = $client->delete('cities/' . $id, $id);

        if ($response && isset($response['success']) && $response['success']) {
            echo "A város sikeresen törölve!";
        }

        $counties = self::getCounties();
        $cities = self::getCities();
        $selectedCountyId = $_POST['selected-county'] ?? null;
        PageCities::render($counties, $cities, $selectedCountyId);
    }

    private static function handleCitySearch($keyword)
    {
        $client = new Client();
        $cities = $client->get('cities');
        $counties = $client->get('counties');

        $filteredCities = array_filter($cities['data'], function ($city) use ($keyword) {
            return (stripos($city['city'], $keyword) !== false) || 
                (stripos($city['zip_code'], $keyword) !== false);
        });

        AbstractPage::displayCitySearchResults($filteredCities, $counties['data']);
    }


    private static function handleSaveEditCity($request)
    {
        $cityId = $request['city_id'];
        $updatedName = $request['city_name'];
        $updatedZipCode = $request['zip_code'];
        $updatedCountyId = $request['county_id'];

        $client = new Client();
        $response = $client->put("cities/$cityId", [
            'city' => $updatedName,
            'zip_code' => $updatedZipCode,
            'id_county' => $updatedCountyId
        ]);

        if ($response && isset($response['success']) && $response['success']) {
            //echo "A város adatai sikeresen módosítva lettek!";
        }
        $counties = self::getCounties();
        $cities = self::getCities();
        PageCities::render($counties, $cities, $updatedCountyId);
    }

    private static function handleEditCity($request)
    {
        $cityId = $request['btn-edit-city'];
        $client = new Client();

        $city = $client->get("cities/$cityId");
        $counties = self::getCounties();

        if ($city && isset($city['data'])) {
            PageCities::displayEditForm($city['data'], $counties); 
        } 
    }

    private static function handleShowCities($request)
    {
        $selectedCountyId = !empty($request['selected-county']) ? intval($request['selected-county']) : null;

        $counties = self::getCounties();
        $cities = self::getCities();

        PageCities::render($counties, $cities, $selectedCountyId);
    }

    private static function handleFilterLetter($request)
    {
        $selectedCountyId = !empty($request['selected-county']) ? intval($request['selected-county']) : null;
        $filterLetter = !empty($request['filter-letter']) ? strtoupper($request['filter-letter']) : null;

        $counties = self::getCounties();
        $cities = self::getCities();

        PageCities::render($counties, $cities, $selectedCountyId, $filterLetter);
    }
}
