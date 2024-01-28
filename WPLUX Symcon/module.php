<?php

class WPLUXSymcon extends IPSModule
{

    private $updateTimer;

    protected function Log($Message)
    {
        IPS_LogMessage(__CLASS__, $Message);
    }

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString('IPAddress', '192.168.178.59');
        $this->RegisterPropertyInteger('Port', 8889);
        $this->RegisterPropertyString('IDListe', '[]');
        $this->RegisterPropertyInteger('UpdateInterval', 0);

        // Timer für Aktualisierung registrieren
        $this->RegisterTimer('UpdateTimer', 0, 'WPLUX_Update(' . $this->InstanceID . ');');
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        // Timer für Aktualisierung aktualisieren
        $this->SetTimerInterval('UpdateTimer', $this->ReadPropertyInteger('UpdateInterval') * 1000);

        // Bei Änderungen am Konfigurationsformular oder bei der Initialisierung auslösen
        $this->Update();
    }

    public function Update()
    {
        //Verbindung zur Lux
        $IpWwc = "{$this->ReadPropertyString('IPAddress')}";
        $WwcJavaPort = "{$this->ReadPropertyInteger('Port')}";
        $SiteTitle = "WÄRMEPUMPE";

        // Integriere Variabelbeschreibung aus Java Daten
        require_once __DIR__ . '/../java_daten.php';

        // Lesen Sie die ID-Liste
        $idListe = json_decode($this->ReadPropertyString('IDListe'), true);

        // Debug-Ausgabe
        $this->Log("ID-Liste: " . print_r($idListe, true));

        // Variablen
        $sBuff = 0;
        $time1 = time();
        $filename = "test.tst";
        $JavaWerte = 0;
        $refreshtime = 5; // Sekunden

        // Connecten
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        $connect = socket_connect($socket, $IpWwc, $WwcJavaPort);

        if (!$connect) {
            $error_code = socket_last_error();
            exit("Socket connect failed with error code: $error_code\n");
        }

        // Daten holen
        $msg = pack('N*',3004);
        $send=socket_write($socket, $msg, 4); //3004 senden

        $msg = pack('N*',0);
        $send=socket_write($socket, $msg, 4); //0 senden

        socket_recv($socket,$Test,4,MSG_WAITALL);  // Lesen, sollte 3004 zurückkommen
        $Test = unpack('N*',$Test);

        socket_recv($socket,$Test,4,MSG_WAITALL); // Status
        $Test = unpack('N*',$Test);

        socket_recv($socket,$Test,4,MSG_WAITALL); // Länge der nachfolgenden Werte
        $Test = unpack('N*',$Test);

        $JavaWerte = implode($Test);

        for ($i = 0; $i < $JavaWerte; ++$i)//vorwärts
        {
            socket_recv($socket,$InBuff[$i],4,MSG_WAITALL);  // Lesen, sollte 3004 zurückkommen
            $daten_raw[$i] = implode(unpack('N*',$InBuff[$i]));
        }
        //socket wieder schliessen
        socket_close($socket);

		// Werte anzeigen
for ($i = 0; $i < $JavaWerte; ++$i) {
    // Testbereich für weitere Variablen basierend auf ID-Liste
    if (in_array($i, array_column($idListe, 'id'))) {
        $idInIdListe = $idListe[array_search($i, array_column($idListe, 'id'))]['id'];

        $minusTest = $daten_raw[$i] * 0.1;
        if ($minusTest > 429496000) {
            $daten_raw[$i] -= 4294967296;
            $daten_raw[$i] *= 0.1;
        } else {
            $daten_raw[$i] *= 0.1;
        }
        $daten_raw[$i] = round($daten_raw[$i], 1);

        // Debug-Ausgabe
        $this->Log("Variable erstellen/aktualisieren für ID: " . $idInIdListe);

        // Direkte Erstellung der Variable mit Ident
        $ident = 'WP_' . $java_dataset[$i];
        $varid = $this->CreateOrUpdateVariable($ident, $daten_raw[$i], $idInIdListe);
    } else {
        // Variable löschen, da sie nicht mehr in der ID-Liste ist
        $this->DeleteVariableIfExists('WP_' . $java_dataset[$i]);
    }
}
		}

		private function CreateOrUpdateVariable($ident, $value, $positionsnummer)
{
    $minusTest = $value * 0.1;
    if ($minusTest > 429496000) {
        $value -= 4294967296;
        $value *= 0.1;
    } else {
        $value *= 0.1;
    }
    $value = round($value, 1);

    // Debug-Ausgabe
    $this->Log("Variable erstellen/aktualisieren für Ident: " . $ident . ", Positionsnummer: " . $positionsnummer);

    // Direkte Erstellung der Variable mit Ident
    $varid = $this->RegisterVariableFloat($ident, $ident);
    
    // Setzen Sie den Objektidentifikator (Ident)
    IPS_SetIdent($varid, (string)$positionsnummer);
    
    SetValueFloat($varid, $value);

    return $varid;
}

		private function DeleteVariableIfExists($ident)
		{
		$variableID = @IPS_GetObjectIDByIdent($ident, $this->InstanceID);
		if ($variableID !== false) {
			// Debug-Ausgabe
			$this->Log("Variable löschen: " . $ident);

			// Variable löschen
			IPS_DeleteVariable($variableID);
		}
		}
		}