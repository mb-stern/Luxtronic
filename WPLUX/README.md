# Modul für Wärmepumpen mit Luxtronik für IP-Symcon
Dieses Modul ermöglicht, Daten der Luxtronik verschiedener Wärmepumpen-Hersteller (zB Alpha InnoTec, Buderus (Logamatic HMC20, HMC20 Z), CTA All-In-One (Aeroplus), Elco, Nibe (AP-AW10), Roth (ThermoAura, ThermoTerra), Novelan (WPR NET) and Wolf Heiztechnik (BWL/BWS)) abzufragen.
Ausserdem ist es geeignet für Besitzer einer PV-Anlage, um die überschüssige Energie gemäss eigenen Vorstellungen der Wärmepumpe zuzuführen. Dies ist möglich durch Anpasen von Warmwasser- und Rücklauf-Solltemperatur.
Um das Modul zu nutzen, muss der in der Luxtronik integrierte RJ45 Netzwerkanschluss mit dem heimschen Netzwerk verbunden werden.
Danach muss sichergestellt werden, dass der Port 8888 (ältere Lux) oder 8889 (neuere Lux) nicht durch die Firewall blockiert ist.
Dieses Modul funktioniert über Java-Abfrage, ab einem gewissen FW-Stand der LUX findet die Abfrage über Websocket statt. Java sollte aber weiterhin funktionieren.
Die Bedeutung und ID's der Variablen sind hier zu finden: https://loxwiki.atlassian.net/wiki/spaces/LOX/pages/1533935933/Java+Webinterface

![image](https://github.com/mb-stern/WPLUX-Symcon/assets/95777848/ff2b9244-a2b8-4d65-9903-bc2464c1c98d)


### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)
8. [Versionen](#8-versionen)

### 1. Funktionsumfang

* Abfrage der Istwerte und Steurung der Luxtronik, welche in verschiedenen Wärmepumpen als Steuerung verbaut ist.
* Es werden automatisch die gewünschten Variablen angelegt und die benötigten Profile erstellt.
* Es werden jedoch nicht restlos alle Werte in Variablen aufgeschlüsselt, bei Bedarf ist daher der Name manuell einzutragen.
* Ebenfalls werden je nach Wärmepumpen-Typ nicht alle Werte geliefert. Offensichtlich werden mit einer Software alle Wärmepumentypen abgedeckt.
* Es können Variablen für die Steuerung von Heizung, Warmwasser und Kühlung aktiviert werden, je nach Funktionsumfang der Wärmepumpe. Diese Variablen zur Steuerung werden nicht live synchronisiert, sondern immer erst dann, wenn Änderungen am Konfigurationsformular vorgenommen wurden.
* Die Anzeige des COP-Faktor ist nun unter Zuhilfenahme einer externen Leistungsmessung (kW) möglich. Die entsprechende Variable kann im Konfigurationsformular ausgewählt werden.
* Die Anzeige des JAZ-Faktor ist nun unter Zuhilfenahme einer externen Leistungsmessung (kWh) möglich. Die entsprechende Variable kann im Konfigurationsformular ausgewählt und die Berechnung bei Bedarf zurückgesetzt werden.
* Es kann die interne Timerfunktion der Luxtronik genutzt werden. Es kann ausgewählt werden, wie viele Variablen (Zeitfenster) erstellt werden sollen, um nicht unnötige Variablen zu verschwenden. Die maximale Menge ist analog den Programmiermöglichkeiten über das Webinterface.

### 2. Voraussetzungen

- IP-Symcon ab Version 7.0

### 3. Software-Installation

* Über den Module Store kann das Modul unter dem Namen Luxtronik gefunden und installiert werden.
* Alternativ über das Module Control folgende URL hinzufügen: https://github.com/mb-stern/Luxtronik

### 4. Einrichten der Instanzen in IP-Symcon

- Unter 'Instanz hinzufügen' kann das 'Luxtronik'-Modul mithilfe des Schnellfilters gefunden werden.  
- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
IP-Adresse      |	IP-Adresse des Rechners auf dem der Libre Hardware Monitor läuft
Port            |   Port der Luxtronic/Wärmepumpe (8888 oder 8889). Der Port muss in der Firewall geöffnet sein
Intervall       |   Intervall für das Update der Werte
Überwachte ID's  |  Hier die gewünschten ID's der Werte. Diese Wert sind hier ersichtlich https://loxwiki.atlassian.net/wiki/spaces/LOX/pages/1533935933/Java+Webinterface

![image](https://github.com/mb-stern/Luxtronik/assets/95777848/a29e9039-9026-49e1-af82-0dac7ca72536)

![image](https://github.com/mb-stern/Luxtronik/assets/95777848/70a03389-272b-49b2-8ef4-81dbeee2633b)


### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Es werden Variablen/Typen je nach Wahl der ID's erstellt. Pro ID wird eine Variable erstellt.

#### Profile

Name   | Typ
------ | ------- 
WPLUX.Imp     |  Integer   
WPLUX.Typ     |  Integer   
WPLUX.Biv     |  Integer   
WPLUX.BZ      |  Integer 
WPLUX.Comf    |  Bool 
WPLUX.Men1    |  Integer
WPLUX.Men2    |  Integer
WPLUX.Men3    |  Integer
WPLUX.Akt     |  Bool
WPLUX.Pres    |  Float
WPLUX.Fan    |  Integer
WPLUX.lh	|  Integer
WPLUX.Wwhe	|  Integer
WPLUX.Kue	|  Integer
WPLUX.Tset	|  Float
WPLUX.Wset	|  Float
WPLUX.Std	|  Integer

### 6. WebFront

Die Variablen zur Steuerung der Luxtronik können aus der Visualisierung heraus gesteuert werden.

### 7. PHP-Befehlsreferenz

`boolean WPLUX_BeispielFunktion(integer $InstanzID);`
Erklärung der Funktion.

Beispiel:
`WPLUX_Update(12345);`

### 8. Versionen

Version 3.4 (07.03.2024)

- Es kann nun die internen Timerfunktionen für Heizung und Warmwasser der LUX gesteuert werden. Eine Anpassung für die gesamte Woche, Mo-Fr/Sa+So und Wochentage analog dem LUX-Timer ist möglich. Um Variabeln zu sparen ist es möglich, nur die gewünschte Anzahl Zeitfenster einzublenden. Beim Ändern oer deaktiviern bleiben aber die gespeicherten Zeiten erhalten
- Wert 56, 58, 60-66 (Betriebsstunden) werden nun in Stunden dargestellt

Version 3.3 (25.02.2024)

- Berechnung des JAZ jetzt durch auswählen einer externen Variable für die Eingangsleistung in kWh möglich. Ebenfalls besteht die Möglichkeit, den JAZ-Faktor zu reseten, um zum Beispiel bei Jahresende oder bei Bedarf die Berechnung neu zu starten.

Version 3.2 (20.02.2024)

- Berechnung des COP jetzt durch auswählen einer externen Variable für die Eingangsleistung in kW möglich.
- Weitere Anpassen der Debug- und der Fehler-Ausgabe.
- Problem behoben, dass unter gewissen Umständen wurde eine falsche Laufzeit von -1h59m berechnet wurde.

Version 3.1 (17.02.2024)

- Anpassen der Debug- und der Fehler-Ausgabe.


Version 3.0 (15.02.2024)

- Modul von WPLUX Symcon in Luxtronik umbenannt um die Store-Kompatibilität zu erreichen. Dies erfordert leider eine Neuinstallation des Moduls und das Transferieren der Variablen-Werte durch den Anwender.
- Code massiv umgebaut um die Store-Kompatibilität zu ereichen
- Es kann eine Variable zur Anpassung der Warmwasser Solltemperatur eingeblendet werden. Sinnvoll für PVA Besitzer, welche überschüssige Energie in den Warmwasserspeicher verschieben möchten. Temperaturbereich 30-65 Grad.
- Umgestaltung des Konfigurationsformulars, die aktivierbaren Variablen zur Steuerung der Luxtronic werden nun in einem ExpansionPanel dargestellt.
- Variable 95 - 115 umbenannt da der lange Name zu Fehler führte (Variablen müssen manuell im Baum gelöscht werden wenn sie bereits vorhanden sind)
- Variable 20 umbenannt wegen ungültigem Sonderzeichen (Variablen müssen manuell im Baum gelöscht werden wenn sie bereits vorhanden sind)
 

Version 2.4 (11.02.2024)

- Erstellung der Variablenprofile von Create() in ApplyChanges() verschoben, damit die Profile bei jeder Änderung auf Vorhandensein geprüft und ggf. erstellt werden.
- Im Integer-Variablenprofil WPLUX.Fan wird die Einheit 'rpm' nun klein geschrieben um ein einheitlicheres Gesamtbild der Werte zu erreichen. Wenn die Kosmetik gewünscht wird, muss das Variablenprofil manuell gelöscht werden. Es wird bei einer Konfigurationsänderung neu erstellt.
- Es kann nun eine Variable zur Anpassung der Temperatur eingeblendet werden. Dieser Wert hebt die Rücklauftemperatur entsprechend an und ermöglicht eine Temperaturanpassung, ohne die Heizkurve zu verändern. Verstellbereich -5 bis +5 Grad.

Version 2.3 (11.02.2024)

- Alle Werte in Sekunden werden nun in Std und Min angezeigt (Wert 56, 58, 60-77, 120, 123, 158, 161)
- Das Integer-Variablenprofil WPLUX.Sec wird bei der Installation nicht mehr erstellt da die Zeitangaben nun als String ausgegeben werden

Version 2.2 (10.02.2024)

- Berechnung für Wert 67 (Waermepumpe_laeuft_seit) und Wert 73 (Verdichter_Standzeit) nun in Stunden und Minuten
- Variablenprofil für Wert 257 angepasst, zeigte kWh statt kW

Version 2.1 (10.02.2024)

- Berechnung für Wert 183 (Steuersignal_Umwaelzpumpe) korrigiert, wurde um Faktor 10 zu gering berechnet

Version 2.0 (10.02.2024)

- Es können nun Variablen für die Modus-Steuerung von Heizung, Warmwasser und Kühlung aktiviert werden.
- Minustest für Aussentemperatur hinzugefügt, so dass plausible Minuswerte ausgegeben werden
- Erhöhung der Java-ID's auf 267 sollte Fehlermeldungen gewisser Luxtronic vermeiden.

Version 1.2 (05.02.2024)

- Muster IP-Adresse wird nicht mehr standardmässig geladen bei Installation des Moduls. Dies führte bei der Installation zu Fehlermeldungen.

Version 1.1 (04.02.2024)

- Kleine Anpassungen an der Variabelzuweisung und Konvertierung
- Vergössertes Fenster Sortierung der ID's im Konfigurationsformular

Version 1.0 (04.02.2024)

- Initiale Version
