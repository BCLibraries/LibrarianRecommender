create table courses
(
    id    integer
        primary key,
    title text not null,
    code  text
);

create index courses_code_index
    on courses (code);

create table subjects
(
    id   INTEGER not null
        primary key autoincrement,
    code TEXT,
    name TEXT
);

create table types
(
    id    TEXT not null
        constraint type_pk
            primary key,
    title TEXT not null
);

create table readings
(
    id      text
        constraint reading_pk
            primary key,
    title   TEXT    not null,
    type_id INTEGER not null
        constraint readings_type_id_fk
            references types,
    creator TEXT
);

create table courses_readings
(
    course_id  integer
        constraint courses_readings_courses_id_fk
            references courses,
    reading_id TEXT
        constraint courses_readings_readings_id_fk
            references readings,
    constraint courses_readings_pk
        primary key (course_id, reading_id)
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

