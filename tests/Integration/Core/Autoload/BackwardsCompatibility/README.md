### Testing plan

Each testsuite in this directory must be run in a separate php process, as otherwise the tests are not independent from eachother.

To achive this execute the shell script runtests_compatibility.sh from this directory.

```
./runtests_compatibility.sh | tee test-results.txt
```
