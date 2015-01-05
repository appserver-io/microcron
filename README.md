# Microcron

[![Latest Stable Version](https://poser.pugx.org/appserver-io/microcron/v/stable.png)](https://packagist.org/packages/appserver-io/microcron)
[![Total Downloads](https://poser.pugx.org/appserver-io/microcron/downloads.png)](https://packagist.org/packages/appserver-io/microcron)
[![License](https://poser.pugx.org/appserver-io/microcron/license.png)](https://packagist.org/packages/appserver-io/microcron)
[![Build Status](https://travis-ci.org/appserver-io/microcron.png)](https://travis-ci.org/appserver-io/microcron)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/appserver-io/microcron/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/microcron/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/appserver-io/microcron/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/microcron/?branch=master)

## Introduction

This library aim for a complete PHP implementation of a cron expression with the addition of seconds as smallest time span to be controlled.
Cron expressions used with this library have, other than [normal cron](https://en.wikipedia.org/wiki/Cron), 6 digits where as the schema starts with seconds and continues with a normal cron schedule.

The `second`-digit can be used the same as the `minute` or `hour` digit.

## Notice

This project is heavily inspired and based on [mtdowling/cron-expression](https://github.com/mtdowling/cron-expression) which provides the technical background and calculation engine.
Therefor the license of this project has to be taken into account.
It is included within the enclosed `LICENSE` file.