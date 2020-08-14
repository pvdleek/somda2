Somda
---

# Introduction

This is the Somda repository. It consists of:
- A web-application with automatic detection of mobile devices.
- A RSS feed-provider with several options.
- An API to be used within other web-applications or mobile apps.

## Installation and configuration

### Pre-requisites

Somda has the following system requirements:
- _PHP_, minimum version 7.4
- PHP extensions _ctype_, _curl_, _gd_, _iconv_ and _json_
- _MySQL_ or _MariaDB_
- A webserver such as _Apache_ or _Nginx_
- Preferably have _ant_ installed: https://github.com/apache/ant

### Getting started

Setting up a local environment requires the following steps:
- Clone the repository
- Copy the file `.env` to `env.local` in the root and adjust it to your environment. The keys WRONG_SPOTS_FORUM_ID, NS_API_PRIMARY_KEY and NS_API_SECONDARY_KEY are not important at this stage.
- Import the database scripts into your local database in this order:
  - `database/empty_database.sql`
  - `database/basic_data.sql`
  - `database/somda_trein.sql`
  - `database/somda_tdr_treinnummerlijst.sql`
  - `database/somda_tdr_trein_treinnummerlijst.sql`
  - `database/somda_tdr_drgl.sql`
  - `database/somda_tdr.sql`
  - `database/somda_tdr_s_e.sql`
- Execute the command `ant setup`
- Execute the command `ant update-database`

## Architecture

Somda is built in the Symfony framework with an MVC architecture.

### Database

The basic layout of the database origins in 2004. Therefore, the design is not that modern at some points. It is on the todo-list to improve that.
New tables in the database should already comply with the guidelines as described below, existing tables will be changed.
- Each table starts with a 3 character abbreviation.
- This abbreviation is short for the rest of the table name:
  - If the table name is 1 word, the first 3 characters are used. For example _sys_system_.
  - If the table name is 2 words, the first 2 characters of the first word are used followed by the first character of the second word. For example: _usp_user_preference_.
  - If the table name is 3 or more words, the first character of the first 3 words is used. For example _spd_system_preference_domain_.
- This abbreviation is also prefixed in all columns.
- In a column with a foreign key, the abbreviation of the other table is used. For example if _usp_user_preference_ has a column with a foreign key to column _id_ of table _pre_preference_, that column is named _usp_pre_id_.

### Code

Somda uses a Model - View - Controller architecture following these guidelines:
- Models have no knowledge of each other and contain only functions that require no knowledge of the outside world. For a good example, look at _isActive_ in _Entity\TrainTableYear_.
- Views contain as little business logic as possible, they basically only display what the controllers give them.
- Controllers contain as little business logic as possible, they only process forms, collect data and hand data to the views. If you need business logic, consider a helper or service.

### Unit-testing

Before running the unit tests, execute `php composer.phar dump-env test`

## Contributing

Somda is not the best code out there. Some of the principles as described are not followed or only halfway.
With each update there will be refactoring to make things better. If you decide to contribute, please make better code than what you found... 

Pull-requests at Github are highly encouraged and will always be reviewed and considered. Please always base them on the develop branch.