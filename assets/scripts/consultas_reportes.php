<?php

$query2 = "SELECT
    subjects.NAME as TALLER,
    subjects_type.TYPE as TIPO,
    schedule.DAY as DIA ,
    schedule.START_HOUR as INICIO,
    schedule.FINISH_HOUR as FINAL,
    schedule.ID_TEACHER as PROFESOR,
    locations.NAME as SEDE
    FROM alumns_by_schedule
    INNER JOIN schedule
    ON schedule.ID = alumns_by_schedule.ID_SCHEDULE
    INNER JOIN subjects  
    ON schedule.ID_SUBJECT = subjects.ID
    INNER JOIN subjects_type
    ON subjects.ID_TYPE = subjects_type.ID_SUBJECTS_TYPE
    INNER JOIN locations
    ON schedule.ID_LOCATION = locations.ID
    WHERE alumns_by_schedule.ID_SCHEDULE = ". $id_schedule. " LIMIT 1";

$resultado = $link->query($query);

?>