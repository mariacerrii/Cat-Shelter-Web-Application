# 🐈 Cat Shelter Management — Web Application

**Academic Web Development Project | Politecnico di Torino**

A database-driven web application developed for the **Progettazione di servizi web e reti di calcolatori** course at Politecnico di Torino.

The project focuses on the design and implementation of a cat shelter management website, combining frontend development, server-side programming, relational databases, and client-server communication.

**Technologies:** HTML, CSS, JavaScript, PHP, MySQL  

---

## 📖 Project Overview

The project was developed to explore the design and implementation of a dynamic, database-driven web application.

The website provides interfaces and application components related to cat shelter activities, including cat information, visit reservations, user registration and authentication, and volunteer management.

The development process involved frontend interface design, backend programming, relational database integration, and the organization of application resources into a structured project.

## 🎯 Main Features

- **Cat Information and Management:** Pages and application logic dedicated to shelter cats.
- **Visit Reservations:** Client-side reservation handling and communication with PHP backend endpoints.
- **User Registration and Authentication:** Registration, sign-in, and logout functionality.
- **Volunteer Management:** Dedicated interfaces and scripts related to volunteer activities.
- **Database Integration:** MySQL-based storage and retrieval of application data.
- **Client-Server Communication:** Asynchronous JavaScript requests to PHP endpoints, including JSON data exchange.

## 🛠️ Technologies & Tools

| Technology | Purpose |
|---|---|
| HTML | Structure and organization of web pages |
| CSS | Styling, layout, and responsive design |
| JavaScript | Client-side interactions, validation, and asynchronous requests |
| PHP | Backend logic, request processing, and server-side functionality |
| MySQL | Relational database management |
| SQL | Database schema and queries |
| Git & GitHub | Version control and source-code management |
| PyCharm | Development environment |

## 🏗️ Application Architecture

The application is organized into different functional components.

### Frontend

The frontend includes PHP-rendered pages, CSS stylesheets, and JavaScript scripts responsible for user interactions and form handling.

### Backend

PHP scripts handle server-side operations and communication with the database.

### Database

A MySQL relational database supports the storage and management of application data. An SQL script is included in the repository.

### Client-Server Communication

JavaScript uses asynchronous requests through the Fetch API to communicate with PHP endpoints. JSON is used for data exchange in relevant application workflows.

## 📂 Project Structure

```text
gattile_sito_2026/
├── includes/
├── public/
│   ├── api/
│   ├── assets/
│   ├── components/
│   ├── home.php
│   ├── logout.php
│   ├── nuovi_gatti.php
│   ├── registrazione.php
│   ├── signin.php
│   └── turni_volontariato.php
├── scripts/
│   ├── nuovi_gatti.js
│   ├── prenotazione.js
│   ├── registrazione.js
│   ├── signin.js
│   └── volontariato.js
├── sql/
│   └── gatti_v1.sql
├── 330365_struttura.txt
├── connessionedb.php
├── progettoSito.txt
└── style.css
```

The project separates public pages, JavaScript functionality, reusable components, backend resources, and database-related files.

## ⚙️ Local Setup

The project requires an environment supporting PHP and MySQL.

1. Clone the repository.
2. Configure a local PHP-compatible web server.
3. Create and initialize a MySQL database using the provided SQL script.
4. Configure the database connection using local credentials.
5. Start the web server and access the application through a browser.

**Note:** The project was developed for an academic environment. Additional configuration may be required to run it on a different system. Database credentials should be stored securely and must not be committed to a public repository.

---

## 🎓 Academic Context

| Information | Details |
|---|---|
| University | Politecnico di Torino, Italy |
| Degree Programme | Bachelor's Degree in Management Engineering |
| Course | Progettazione di servizi web e reti di calcolatori |
| Course Code | 01NBEPL |
| Professor | Andrea Atzeni |
| Credits | 8 ECTS |
| Project Type | Academic Web Application |

The project provided practical experience in web application development, database integration, frontend/backend communication, and software organization.

---

## 📊 Academic Evaluation & Feedback

The project received a final evaluation of **24/30**.

The professor's feedback highlighted positive aspects of the implementation while identifying opportunities for improvement in code quality, accessibility, responsiveness, and software engineering practices.

### Strengths Identified

- **Effective Interface Layout:** The website was considered well-structured and visually coherent.
- **Project Organization:** Files were generally organized effectively into directories.
- **Code Documentation:** Explanatory comments were present throughout the source code.
- **Design Documentation:** The project included a generally satisfactory description of its design decisions.
- **Responsive Design:** Adaptation to different viewport sizes was implemented, although with limitations.

### Areas for Improvement

| Area | Feedback and Potential Improvements |
|---|---|
| Visual Assets | Review generated imagery to ensure visual consistency and quality |
| Favicon | Add a favicon to improve browser identification and usability |
| CSS Validation | Correct CSS validation errors and improve standards compliance |
| CSS Readability | Refactor stylesheets for clarity and maintainability |
| Responsive Design | Improve layouts for very small and very large viewports |
| Relative Units | Prefer relative CSS units where appropriate |
| PHP Architecture | Review unnecessary `require_once` statements and their performance impact |
| Code Robustness | Improve validation, error handling, and reliability |
| User Management | Refine the handling of users who are already registered |
| Project Structure | Align the placement of design documentation with assignment specifications |
| Technical Documentation | Expand explanations of security decisions and communication between application components |

### 💡 Lessons Learned

The evaluation highlighted the distinction between implementing a functional web application and developing one that is also robust, maintainable, accessible, and compliant with technical standards.

The most relevant lessons include:

1. **Code quality matters beyond functionality.** Readability, validation, and maintainability are essential aspects of software development.
2. **Responsive design requires systematic testing.** Supporting multiple viewport sizes involves more than implementing flexible layouts.
3. **Software architecture affects performance.** Backend organization and dependency management should be evaluated critically.
4. **User experience includes small details.** Visual assets, browser icons, and clear error handling contribute to overall application quality.
5. **Technical documentation should explain design trade-offs.** Security considerations and client-server communication deserve explicit discussion.

These observations provide a foundation for future improvements and reflect the learning process associated with the project.

**Status:** Original academic submission. The improvements described above represent identified opportunities and are not necessarily implemented in the current version.

---

## 🔧 Potential Future Improvements

Future development could focus on:

- Validating and refactoring the CSS.
- Improving responsive behavior across devices.
- Adding a favicon and reviewing graphical assets.
- Simplifying PHP dependencies and improving code organization.
- Strengthening server-side validation and error handling.
- Improving registration and authentication workflows.
- Expanding security and architecture documentation.
- Reviewing accessibility and usability.

---

## 📌 Repository Notes

- This repository documents an academic project and its original implementation.
- Source code and internal comments may be written in Italian.
- The application requires PHP and MySQL configuration.
- The project is provided for educational and portfolio purposes.
- The evaluation and improvement areas are included to document the development and learning process transparently.

## 👩‍💻 Author

**Maria Cerri**  
Management Engineering — Politecnico di Torino

[GitHub Profile](https://github.com/mariacerrii)

---

*Academic Web Development Project — Politecnico di Torino*
