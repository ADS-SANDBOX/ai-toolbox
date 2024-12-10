.PHONY: build build-no-cache up down composer artisan-migrate artisan-migrate-rollback scribe autoload

DOCKER_COMPOSE_FILE := .docker/docker-compose.yml
NGINX_TEMPLATE := .docker/project/nginx/nginx.template
NGINX_CONF := .docker/project/nginx/nginx.conf
ENV_FILE := .docker/.env
ENV_EXAMPLE := .docker/.env.example
CODE_DIR := ./code

-include $(ENV_FILE)
export

DC := docker-compose -f $(DOCKER_COMPOSE_FILE)
EXEC := $(DC) exec php sh -c

docker-env:
	@if [ ! -f "$(ENV_FILE)" ] && [ -f "$(ENV_EXAMPLE)" ]; then \
		echo "Copying $(ENV_EXAMPLE) to $(ENV_FILE)..."; \
		cp "$(ENV_EXAMPLE)" "$(ENV_FILE)"; \
	fi

envsubst-nginx:
	source $(ENV_FILE) && envsubst '$$COMPOSE_PROJECT_NAME' < $(NGINX_TEMPLATE) > $(NGINX_CONF)

build:
	$(MAKE) docker-env
	$(MAKE) envsubst-nginx
	$(DC) build

build-no-cache:
	$(MAKE) docker-env
	$(MAKE) envsubst-nginx
	$(DC) build --no-cache

up:
	$(MAKE) docker-env
	$(MAKE) envsubst-nginx
	$(DC) up -d

down:
	$(DC) down


define run_in_code
@if [ -d "$(CODE_DIR)" ]; then \
	$(EXEC) "cd $(CODE_DIR) && $(1)"; \
else \
	echo "The 'code' folder does not exist."; \
fi
endef

init:
	@if [ ! -f "$(CODE_DIR)/.env" ] && [ -f "$(CODE_DIR)/.env.example" ]; then \
		cp "$(CODE_DIR)/.env.example" "$(CODE_DIR)/.env"; \
		echo "Laravel .env file created"; \
	fi
	$(MAKE) composer
	$(call run_in_code,php artisan key:generate)
	$(call run_in_code,php artisan jwt:secret --force)
	$(MAKE) artisan-migrate
	$(MAKE) autoload
	$(MAKE) artisan-scribe
	@echo "Laravel and JWT keys generated successfully"

composer:
	$(call run_in_code,composer install)

artisan-migrate:
	$(call run_in_code,php artisan migrate)

artisan-migrate-rollback:
	$(call run_in_code,php artisan migrate:rollback)

artisan-scribe:
	$(call run_in_code,php artisan scribe:generate)

autoload:
	$(call run_in_code,composer dump-autoload)
	$(call run_in_code,php artisan cache:clear)
	$(call run_in_code,php artisan view:clear)
	$(call run_in_code,php artisan config:clear)

test:
	$(call run_in_code,php artisan test)

feature-test:
	$(call run_in_code,php artisan test --testsuite=Feature)

unit-test:
	$(call run_in_code,php artisan test --testsuite=Unit)

integration-test:
	$(call run_in_code,php artisan test --testsuite=Integration)
