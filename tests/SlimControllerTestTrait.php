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

    protected static $MOVIERATINGS_POST = [
        "data" => [
            "type" => "movieratings",
            "attributes" => [
                "average_rating" => "5",
                "total_ratings" => "1"
            ],
            "relationships" => [
                "movies" => [
                    "data" => [
                        "type" => "movies",
                        "id" => "2"
                    ]
                ]
            ]
        ]
    ];

    protected static $MOVIERATINGS_PATCH = [
        "data" => [
            "type" => "movieratings",
            "id" => "1",
            "attributes" => [
                "average_rating" => "5",
                "total_ratings" => "4"
            ],
            "relationships" => [
                "movies" => [
                    "data" => [
                        "type" => "movies",
                        "id" => "1"
                    ]
                ]
            ]
        ]
    ];
}
