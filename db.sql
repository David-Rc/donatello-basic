CREATE TABLE lo_categories
(
    id_category INT(11) PRIMARY KEY NOT NULL,
    name VARCHAR(100) NOT NULL,
    id_project INT(11) NOT NULL,
    notes TEXT,
    CONSTRAINT categories_lo_projects_fk FOREIGN KEY (id_project) REFERENCES lo_projects (id_project)
);
CREATE INDEX categories_lo_projects_fk ON lo_categories (id_project);
CREATE TABLE lo_projects
(
    id_project INT(11) PRIMARY KEY NOT NULL,
    title VARCHAR(50) NOT NULL,
    notes TEXT,
    creation_date DATETIME
);
CREATE INDEX projects_title_index ON lo_projects (title);
CREATE TABLE lo_tasks
(
    id_task INT(11) PRIMARY KEY NOT NULL,
    title VARCHAR(256) NOT NULL,
    added DATETIME NOT NULL,
    completed DATETIME,
    due DATETIME,
    id_category INT(11) NOT NULL,
    CONSTRAINT lo_tasks__fk FOREIGN KEY (id_category) REFERENCES lo_categories (id_category)
);
CREATE INDEX lo_tasks__fk ON lo_tasks (id_category);
CREATE INDEX tasks_title_index ON lo_tasks (title);
CREATE TABLE lo_users
(
    id_user INT(11) PRIMARY KEY NOT NULL,
    login VARCHAR(256) NOT NULL,
    password VARCHAR(40) NOT NULL,
    username VARCHAR(100) NOT NULL
);
CREATE UNIQUE INDEX users_login_uindex ON lo_users (login);
CREATE UNIQUE INDEX users_username_uindex ON lo_users (username);
CREATE TABLE lo_users_projects
(
    id_user INT(11) NOT NULL,
    id_project INT(11) NOT NULL,
    CONSTRAINT lo_projects__fk FOREIGN KEY (id_project) REFERENCES lo_projects (id_project),
    CONSTRAINT lo_users__fk FOREIGN KEY (id_user) REFERENCES lo_users (id_user)
);
CREATE INDEX lo_projects__fk ON lo_users_projects (id_project);
CREATE INDEX lo_users__fk ON lo_users_projects (id_user);