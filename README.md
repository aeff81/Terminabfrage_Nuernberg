Vor kurzem stand ich vor der Herausforderung stand ein reimportiertes Fahrzeug in Nürnberg zulassen zu müssen.
Aufgrund der ausländischen Fahrzeugpapiere war eine Onlinezulassung nicht möglich.

Der nächste freie Vor-Ort-Termin war ca. 2,5 Wochen später. Jedoch werden bereits reservierte Termine umgehend wieder freigegeben, wenn diese storniert werden.
Um nicht ständig auf die Webseite starren zu müssen, habe ich dieses Script geschrieben und in einer Endlosschleife

while true; do php termine.php; sleep 60; done

in einem Screen laufen lassen. Ein Cronjob wäre auch möglich gewesen, jedoch wollte ich gelegentlich eben doch manuell die Ausgabe checken.

Das Script fragt den Microservice der Stadt ab, gibt die Termine auf stdout aus und schickt, wenn ein Termin in einem definierten Zeitfenster enthalten ist, eine E-Mail.
Diese Vorgaben lassen sich mit den Variablen im oberen Teil konfigurieren.


Das Script war eine Quick-and-Dirty Wegwerflösung (wie oft muss man schon ausländische Fahrzeuge zulassen?). Aber vielleicht kann sie jemand brauchen.
Bitte die GPLv2 beachten!!
