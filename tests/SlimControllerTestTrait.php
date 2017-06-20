<?php declare(strict_types=1);

namespace RestSample\Tests;

trait SlimControllerTestTrait
{
    protected static $MOVIES_GET = [
        "data" => [[
            "type" => "movies",
            "id" => "1",
            "attributes" => [
                "name" => "Avatar"
            ],
        ]]
    ];

    protected static $MOVIERATINGS_GET = [
        "data" => [[
            "type" => "movieratings",
            "id" => "1",
            "attributes" => [
                "average_rating" => "4",
                "total_ratings" => "3"
            ],
            "relationships" => [
                "movies" => [
                    "data" => [
                        "type" => "movies",
                        "id" => "1"
                    ]
                ]
            ]
        ]]
    ];
}
