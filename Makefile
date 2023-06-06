# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
PHPUNIT      = $(PHP) bin/phpunit
PHPSTAN      = $(PHP) vendor/bin/phpstan

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc composer-install create-database load-fixtires test coverage-report run-project generate-keypair

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

composer-install: ## Install project dependencies using Composer
	@$(COMPOSER) install

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

create-database: ## Create the database
	@$(SYMFONY) doctrine:database:create
	@$(SYMFONY) doctrine:database:create --env=test

load-fixtures: ## Load fixtures
	@$(SYMFONY) doctrine:fixtures:load --no-interaction

generate-keypair: ## Generate JWT key pair
	@$(SYMFONY) lexik:jwt:generate-keypair

## —— Tests 🧪 ————————————————————————————————————————————————————————————————
test: ## Run PHPUnit tests and PHPStan analysis
	@$(PHPUNIT)
	@$(PHPSTAN) analyse src --memory-limit 1G

coverage-report: ## Generate PHPUnit code coverage report
	@$(DOCKER_COMP) run --rm -e XDEBUG_MODE=coverage php bin/phpunit --coverage-html coverage-report
	@echo "Code coverage report generated. You can view it by opening the following file:"
	@echo "file://$(CURDIR)/coverage-report/index.html"


## —— Run Project 🚀 ———————————————————————————————————————————————————————————————
run-project: start composer-install generate-keypair create-database load-fixtures ## Setup and run
