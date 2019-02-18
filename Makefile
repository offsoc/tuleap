RPM_TMP=$(HOME)/rpmbuild
PKG_NAME=tuleap-plugin-baseline
VERSION=$(shell LANG=C cat VERSION)
# This meant to avoid having git in the docker container
# RELEASE is computed by launcher (for instance jenkins) where git is installed
# and the passed as an absolute value
ifeq ($(RELEASE),)
	RELEASE=1
endif

BASE_DIR=$(shell pwd)
RPMBUILD=rpmbuild --define "_topdir $(RPM_TMP)"

NAME_VERSION=$(PKG_NAME)-$(VERSION)

compute_version: ## Compute the package version as in the RPM package and in Tuleap Admin interface
	@PACKAGE_VERSION="$$(cat VERSION | tr -d '[[:space:]]')";\
		RELEASE=1;\
		LAST_TAG="$$(git describe --abbrev=0 --tags)";\
		if [ "$$LAST_TAG" == "$$PACKAGE_VERSION" ]; then\
    		NB_COMMITS=$$(git log --oneline "$$LAST_TAG"..HEAD | wc -l);\
    		if [ $$NB_COMMITS -gt 0 ]; then\
	    		RELEASE=$$(($$NB_COMMITS + 1));\
    		fi;\
		fi;\
	echo $$PACKAGE_VERSION-$$RELEASE

deploy-githooks:
	@if [ -e .git/hooks/pre-commit ]; then\
		echo "pre-commit hook already exists";\
	else\
		{\
			echo "Creating pre-commit hook";\
			ln -s ../../tools/utils/githooks/hook-chain .git/hooks/pre-commit;\
		};\
	fi

all:
	$(MAKE) rpm

rpm: $(RPM_TMP)/RPMS/noarch/$(NAME_VERSION)-$(RELEASE).noarch.rpm
	@echo "Results: $^"

$(RPM_TMP)/RPMS/noarch/%.noarch.rpm: $(RPM_TMP)/SRPMS/%.src.rpm
	$(RPMBUILD) --rebuild $<

$(RPM_TMP)/SRPMS/%-$(VERSION)-$(RELEASE).src.rpm: $(RPM_TMP)/SPECS/%.spec $(RPM_TMP)/SOURCES/%-$(VERSION).tar.gz
	$(RPMBUILD) -bs $(RPM_TMP)/SPECS/$*.spec

$(RPM_TMP)/SPECS/%.spec: $(BASE_DIR)/%.spec
	cat $< | \
		sed -e 's/@@VERSION@@/$(VERSION)/g' |\
		sed -e 's/@@RELEASE@@/$(RELEASE)/g' \
		> $@

.PHONY: build
build:
	cd /build/src && npm install && npm run build && \
	cd /build/src/plugins/baseline/scripts && npm install && npm run build && \
	cd /build/src/plugins/baseline/ && composer install --classmap-authoritative --no-dev --no-interaction --no-scripts

$(RPM_TMP)/SOURCES/$(NAME_VERSION).tar.gz: build $(RPM_TMP)
	[ -h $(RPM_TMP)/SOURCES/$(NAME_VERSION) ] || ln -s $(BASE_DIR) $(RPM_TMP)/SOURCES/$(NAME_VERSION)
	[ ! -d $(RPM_TMP)/SOURCES/$(NAME_VERSION)/assets ] || rm -rf $(RPM_TMP)/SOURCES/$(NAME_VERSION)/assets
	cp -ar $(BASE_DIR)/../../src/www/assets/baseline $(RPM_TMP)/SOURCES/$(NAME_VERSION)/assets
	cd $(RPM_TMP)/SOURCES && \
	    find $(NAME_VERSION)/ \(\
	    -path $(NAME_VERSION)/tests -o\
	    -name '*.spec' -o\
	    -name 'Makefile' -o\
	    -name 'build-rpm.sh' -o\
	    -name ".git" -o\
	    -name ".gitignore" -o\
	    -name ".gitmodules" -o\
	    -name "*~" -o\
	    -path "*/.DS_Store"-o\
	    -path "nbproject"-o\
	    \)\
	    -prune -o -print \
		|\
		cpio -o -H ustar --quiet |\
		gzip > $(RPM_TMP)/SOURCES/$(NAME_VERSION).tar.gz

$(RPM_TMP):
	@[ -d $@ ] || mkdir -p $@ $@/BUILD $@/RPMS $@/SOURCES $@/SPECS $@/SRPMS $@/TMP

docker-run:
	@[ -n "$(GID)" -a -n "$(UID)" ] || (echo "*** ERROR: UID or GID are missing" && false)
	useradd -d /build -m build
	cp -Rf /tuleap/ /build/src && cp -Rf /plugin/ /build/src/plugins/baseline && chown -R build /build/src
	su --login --command "make -C /build/src/plugins/baseline all RELEASE=$(RELEASE)" build
	install -o $(UID) -g $(GID) -m 0644 /build/rpmbuild/RPMS/noarch/*.rpm /output

start-sonarqube: ## Start Sonarqube server
	@docker-compose up -d sonarqube
	@echo "Sonarqube is starting.... Go to http://localhost:9000"

stop-sonarqube: ## Start Sonarqube server
	@docker-compose down

sonarscanner: ## Analyze code with Sonarqube (Sonarqube must be started)
	@docker-compose run sonarscanner
