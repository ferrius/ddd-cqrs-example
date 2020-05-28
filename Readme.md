### About
This is an example of implementation and my vision of **practical** CQRS, DDD, ADR, hexagonal architecture and directory structure.
Project has one entity `Task`. Have a basic CRUD operations and make tasks done and decline. 
All UI is are the REST endpoints.

### How to install the project
* `bash setup_env.sh dev` - to setup .env.local docker/.env
* `make dc_up` - docker-compose up 
* `make setup_dev` - composer install, migrations and so on
* `make run php bin/console app:create-user` - create a user
* `http://127.0.0.1:888/api/doc` `https://127.0.0.1:444/api/doc` - api doc

### Implementation
Used symfony messenger component to create transactional command bus, query bus and event bus.
Query model represented by DTOs. Domain and Command layers are covered by unit tests. 

```
├── Core (Core bounded context)
│   ├── Application
│   │   ├── Command
│   │   │   ├── AuthToken
│   │   │   ├── Task
│   │   │   └── User
│   │   ├── Query
│   │       └── Task
│   ├── Domain
│   │   └── Model
│   │       ├── Task
│   │       └── User
│   ├── Infrastructure
│   │   └── Repository
│   └── UI
│       ├── Cli
│       └── Rest
└── Shared
    ├── Domain
    └── Infrastructure

```


