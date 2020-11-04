<p align="center"><a href="https://see.asseco.com" target="_blank"><img src="https://github.com/asseco-voice/art/blob/main/evil_logo.png" width="500"></a></p>

# Broadcast Eloquent events

By default, Laravel [Eloquent events](https://laravel.com/docs/8.x/eloquent#events) are 
dispatched internally. This package enables the events to be dispatched to an event bus
instead.

## Installation

Require the package with ``composer require asseco-voice/laravel-code-quality``.
Service provider for Laravel will be installed automatically.

## Usage

Make the model use a ``BroadcastsEloquentEvents`` and `created`, `updated` and `deleted`
events will be automatically dispatched. 

If you want to modify or add new events to list, publish the configuration with:

    php artisan vendor:publish --provider="Voice\EloquentEventBroadcaster\BroadcasterServiceProvider"
