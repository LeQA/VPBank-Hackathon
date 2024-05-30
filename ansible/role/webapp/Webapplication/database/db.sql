create database IF NOT EXISTS appdatabase;
CREATE USER 'admin'@'%' IDENTIFIED BY 'Password@123';
GRANT SELECT, INSERT ON appdatabase.* TO 'admin'@'%';

USE appdatabase;

CREATE TABLE users (
    user_id varchar(255) primary key,
    username varchar(50) unique not null,
    password varchar(255) not null
);

INSERT INTO users (user_id, username, password) VALUES ('21232f297a57a5a743894a0e4a801fc3', 'admin', 'Password@123');

CREATE TABLE posts (
  post_id char(32) primary key,
  content text,
  author_id varchar(255) not null,
  public tinyint(1) not null
);