<p align="center"><a href="https://see.asseco.com" target="_blank"><img src="https://github.com/asseco-voice/art/blob/main/evil_logo.png" width="500"></a></p>

# Broadcast Eloquent events

By default, Laravel [Eloquent events](https://laravel.com/docs/8.x/eloquent#events) are 
dispatched internally. This package enables the events to be dispatched to an event bus
instead.

## Installation

Require the package with ``composer require asseco-voice/laravel-eloquent-event-broadcaster``.
Service provider for Laravel will be installed automatically.

Using [STOMP](https://github.com/asseco-voice/laravel-stomp) as a queue driver will support
the communication between multiple Laravel services because it will be dispatching events with
raw data so that it doesn't break on the other side. 

This is completely optional however. If you're not using Laravel for microservices, your data
will be serialized the standard Laravel way.  

## Usage

Make the model use a ``BroadcastsEloquentEvents`` and `created`, `updated`, `deleted` and `restored`
events will be automatically dispatched to a ``eloquent::model_events`` queue.

Raw data being sent consists of:

- `payload` key containing original model serialized to array.
- Let your model implement ``AppendsData`` interface to append additional data to payload.
- Changes (`_changes`) array holding `old`/`new` values and `type`/`ID` of entity
that changed the model. Used for auditing purposes (can be disabled through config).
- Headers consisting of:
    - ``service`` - lowercase snake app name
    - ``model`` - full model namespace and model name
    - ``action`` - which action was performed on the model (pulled from 
    registered actions which can be appended or modified in the config)
- Let your model implement ``AppendsHeaders`` interface to append additional headers.

You can tweak the configuration publishing it with:

    php artisan vendor:publish --tag=asseco-broadcaster-config
