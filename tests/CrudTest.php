<?php

use PHPUnit\Framework\TestCase;

class CrudTest extends TestCase
{
    private $db;

    protected function setUp(): void //Létrehoz egy in-memory SQLite adatbázist. Egy counties nevű táblát hoz létre, és egy alapértelmezett adatot (Budapest) szúr be.
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->db->exec("CREATE TABLE counties (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT
        )");

        // Tesztadat beszúrása
        $this->db->exec("INSERT INTO counties (name) VALUES ('Budapest')");
    }

    public function testReadCounty() //Ellenőrzi, hogy a Budapest megye valóban létezik-e az adatbázisban.
    {
        $stmt = $this->db->query("SELECT * FROM counties WHERE name = 'Budapest'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($result);
        $this->assertEquals('Budapest', $result['name']);
    }

    public function testCreateCounty() //Ellenőrzi, hogy új megyét lehet-e hozzáadni (Pest).
    {
        $this->db->exec("INSERT INTO counties (name) VALUES ('Pest')");

        $stmt = $this->db->query("SELECT * FROM counties WHERE name = 'Pest'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($result);
        $this->assertEquals('Pest', $result['name']);
    }

    public function testUpdateCounty() //Ellenőrzi, hogy egy meglévő megye neve frissíthető-e.
    {
        $this->db->exec("UPDATE counties SET name = 'Bács-Kiskun' WHERE name = 'Budapest'");

        $stmt = $this->db->query("SELECT * FROM counties WHERE name = 'Bács-Kiskun'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($result);
        $this->assertEquals('Bács-Kiskun', $result['name']);
    }

    public function testDeleteCounty() //Ellenőrzi, hogy egy megyét törölni lehet-e az adatbázisból.
    {
        $this->db->exec("DELETE FROM counties WHERE name = 'Budapest'");

        $stmt = $this->db->query("SELECT * FROM counties WHERE name = 'Budapest'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertFalse($result);
    }
}

/*Eredmény:
    Biztosítja, hogy az SQLite adatbázisban végzett CRUD műveletek helyesen működnek.
 */