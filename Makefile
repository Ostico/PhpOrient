
# Run the tests
test:
	@./vendor/bin/phpunit

# Re Run the tests every 5 seconds

watch:
	@watch -n 5  make test

coverage:
	@./vendor/bin/phpunit --coverage-html ./coverage

.PHONY: test install install-composer coverage
