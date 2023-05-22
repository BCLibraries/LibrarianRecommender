create table subjects
(
    id   INTEGER not null
        primary key autoincrement,
    code TEXT,
    name TEXT
);

create table types
(
    id   INTEGER not null
        constraint type_pk
            primary key autoincrement,
    name TEXT    not null
);

create table readings
(
    id      INTEGER
        constraint reading_pk
            primary key autoincrement,
    mms     TEXT,
    title   TEXT    not null,
    type_id INTEGER not null
        constraint readings_type_id_fk
            references types
);

create table readings_subjects
(
    reading_id INTEGER
        constraint readings_subjects_readings_id_fk
            references readings,
    subject_id INTEGER not null
        constraint readings_subjects_subjects_id_fk
            references subjects,
    constraint readings_subjects_pk
        primary key (reading_id, subject_id)
);


