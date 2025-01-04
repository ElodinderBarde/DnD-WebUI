Aufbau und Idee:


Das ist der Prototyp eines User Interface(UI) welches helfen soll eine DnD Kampagne zu leiten

Das gesammte Programm basiert auf einer abfolge von Sortierungen, wonach die container interaktiv sind.


dnd_tool/index

das main UI mit den buttons cities / villages können auf die in der Datenbank hinterlegten Städte und Dörfer zugreifen. mit dieser location_ID können die shop_types geladen werden. 

die shop_type_ID wird als FK in shops hinterlegt.

das gleiche geschieht mit Items / customer / employee / characterbogen / sats und cityMap


im grunde beruht es immer auf vorsortierung. 


------

die Buttons Generator/NPCList / Quest / Main sind noch nicht aktiv. 


------------------------------------------

/dnd_tool/Character%20Generator/generator_index.php





Das ist das Kernstück des UI

Die Idee ist es anhand der hinterlegten Daten einen individuellen NPC mit bild zu generieren.



Optionale Eigenschaften:
- Klassen Erlauben ( wählt zufällig eine Klasse und Subklasse)
- Klasse manuell auswählen ( aktiviert Dropdown für Manuelle wahl für MAIN und Subklasse)
- Volk Manuell wählen ( aktiviert Dropdown)
- Familie Generieren ( Generiert zum MainNPC 3-9 NPC in der Rolle "Parent"/"Child"/"Grandparent"/"Aunt_or_Uncle"







welche Daten werden Generiert:



race
npc_class
npc_subclass
npc_fullname
npc_gender
npc_age
NPC_betonung
npc_talkingstyle
npc_background
npc_personality
npc_likes
npc_dislikes
npc_haircolor
npc_hairstyle
npc_beardstyle (if male)
npc_jackets
npc_trousers
npc_keidungsqualität
npc_flaw
npc_ideals
npc_jewellery
npc_other_description
npc_role




nachdem der NPC erstellt wurde, hat man die Möglichkeit dieser direkt einem ort /shoptype / shop zuzuordnen mit der Rolle:

Kunde: 

-Stammkunde
-Reisender
-Anderer

Employee:
-Inhaber
-Lehrling
-Koch
-Geselle
etc....


-----------



aktuelles Problem:


ich möchte die Daten in eine CSV Datei speichern, ehe es in die Datenbank geht.




aus diesen Generierten daten sollten im anschluss promts für eine AI zusammengestellt werden, welche ein Bild generiert.

Um die DB nicht mit unbrauchbaren Daten zu vermüllen, sollen diese erst in einem Cache ordner (\dnd_tool\Media\NPC's\chache) zwischengespeichert werden und nach Überprüfung in den saved Ordner gespeichert werden. 


um sicherzustellen, dass mehrere daten gleichzeitig eingefügt werden können (DB) wurden zwei csv Dateien erstellt. 

csv 1 (npc_data.csv)
csv 2 (family_data) 


funktionierender SQL promt:

use dnd;
INSERT INTO npc (
                npc_fullname_ID, npc_gender_ID, npc_age_ID, race_ID, npc_class_ID,npc_subclass_ID, npc_background_ID,
                npc_beardstyle_ID, npc_betonung_ID, npc_dislikes_ID, npc_haircolor_ID, npc_hairstyle_ID,
                npc_jackets_ID, npc_kleidungsqualität_ID, npc_likes_ID, npc_personality_ID, npc_talkingstyle_ID,
                npc_trousers_ID, npc_ideals_ID, npc_jewellery_ID, npc_flaw_ID, npc_other_description_ID
            ) VALUES (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22);


