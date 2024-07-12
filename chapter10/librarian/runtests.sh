for testcase in $(ls tests);
do 
    echo $testcase
	vendor/bin/phpunit tests/$testcase
	rm -rf .phpunit.cache/
	kill -9 $(ps -C "php" | tail -n 1 | awk '{print $1}')
done
