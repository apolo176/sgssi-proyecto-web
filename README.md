# 1. Introduction

In this repository you'll find the project made by *Machacas* group for SGSSI subject of the third year of Bachelor's Degree in Computer Engineering for Management and Information Systems. Here you won't see the best coding practices or our best abilities. We've done the best we could knowing anything but the basics of web programming in terms of visual styles, efficiency or security.

The main goal of this project has been learning about website security. In fact, the first version of the website is the most unsecure one. We have had to make it like this in order to let our classmates attack the web so we could find and identify the security holes.

# 2. Used technologies

In general, we've used several technologies for the different aspects of the project:

- HTML5: Webpages design.

- CSS: Webpages style.

- JavaScript: Client logic features such as register validations.

- PHP 8.1: Server backend logic features such us database queries.

- MariaDB 10.8.2: Server information storage.

# 3. How to launch the project

Here we will briefly explain how to launch this project on your local machine. We've tried explaining everything the easiest way so a person without technical knowledge could run the project.

### Requirements

- Having at least Docker 28.4.0 and Docker Compose 2.39.2 installed.

- Having ports 81 (web) and 8890 (phpMyAdmin) free

### How to clone the project via HTTPS

1. Locate you wherever you want the project to be downloaded

2. Execute the next command to download the project:

```bash
$ git clone https://github.com/apolo176/sgssi-proyecto-web.git
```
3. Enter the project directory:
```bash
$ cd sgssi-proyecto-web.git
```
4. Deploy the project using Docker Compose in detached mode (in background mode):
```bash
$ docker compose up -d
```
5. The project should have already been deployed successfully. If you want to stop it use the next command:
```bash
$ docker compose stop
```
6. If you want to stop and delete the containers use this instead:
```bash
$ docker compose down
```
7. To access the website and phpMyAdmin for database modifications access via the next URLs:

- Web: http://localhost:81

- phpMyAdmin: http://localhost:8890 (user: admin, password: test)

*If you are looking for phpMyAdmin, take a look at [this](https://github.com/celsocelante/docker-lamp/issues/2).*


# 4. Authors

The project has been made with the collaboration and hard work of the next group of people:

- Eder Torres

- Liviu Deleanu

- Jon Requies

- Alex Isasi

- Iker Ciordia
