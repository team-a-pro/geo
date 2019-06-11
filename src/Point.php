<?php

namespace TeamA\Geo;

class Point
{
    public const EARTH_RADIUS = 6371210.0;

    /**
     * @var float
     */
    protected $_longitude;

    /**
     * @var float
     */
    protected $_latitude;

    protected function __construct(float $longitude, float $latitude)
    {
        $this->setLongitude($longitude);
        $this->setLatitude($latitude);
    }

    /**
     * @param float | int | string | null $longitude
     * @param float | int | string | null $latitude
     */
    public static function create($longitude, $latitude) : ? self
    {
        $longitude = self::_normalizeParam($longitude);
        $latitude  = self::_normalizeParam($latitude);

        if ($longitude === null || $latitude === null) {
            return null;
        }

        try {
            return new self($longitude, $latitude);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param float | int | string | null $param
     */
    protected static function _normalizeParam($param) : ? float
    {
        switch (true) {
            case is_float($param):
            case $param === null;
                return $param;

            case is_string($param):
                $param = trim($param);

                return strlen($param)
                    ? (float) str_replace(',', '.', $param)
                    : null
                ;

            case is_int($param):
                return (float) $param;

            default:
                return null;
        }
    }

    /**
     * Direct geodesic problem.
     *
     * @throws \Exception
     */
    public static function createByBearing(Point $startPoint, float $bearing, float $distance) : Point
    {
        if ($bearing < 0 || $bearing > 360) {
            throw new \Exception('Bearing must be between 0 and 360.');
        }

        if ($distance < 0) {
            throw new \Exception('Distance must be equal or greater than 0.');
        }

        $lat1    = $startPoint->getLatitudeInRadians();
        $long1   = $startPoint->getLongitudeInRadians();
        $bearing = deg2rad($bearing);

        $cl1 = cos($lat1);
        $sl1 = sin($lat1);

        $dr = $distance / self::EARTH_RADIUS;
        $sinDr = sin($dr);
        $cosDr = cos($dr);

        $lat2 = asin(
            $sl1 * $cosDr +
            $cl1 * $sinDr * cos($bearing)
        );

        $long2 = $long1 + atan2(
            sin($bearing) * $sinDr * $cl1,
            $cosDr - $sl1 * sin($lat2)
        );

        return Point::create(
            self::normalizeLongitude(rad2deg($long2)),
            self::normalizeLongitude(rad2deg($lat2))
        );
    }

    public static function normalizeLatitude($latitude) : float
    {
        $result = self::_normalizeParam($latitude);

        while ($result > 90.0 || $result < -90.0) {
            if ($result > 90.0) {
                $result -= 180.0;
            }

            if ($result < -90.0) {
                $result += 180.0;
            }
        }

        return $result;
    }

    public static function normalizeLongitude($longitude) : float
    {
        $result = self::_normalizeParam($longitude);

        while ($result > 180.0 || $result < -180.0) {
            if ($result > 180.0) {
                $result -= 360.0;
            }

            if ($result < -180.0) {
                $result += 360.0;
            }
        }

        return $result;
    }

    public function getLatitude() : float
    {
        return $this->_latitude;
    }

    public function getLatitudeInRadians() : float
    {
        return deg2rad($this->_latitude);
    }

    /**
     * @throws \Exception
     */
    public function setLatitude(float $latitude)
    {
        if ($latitude < -90.0 || $latitude > 90.0) {
            throw new \Exception();
        }

        $this->_latitude = $latitude;
        return $this;
    }

    public function getLongitude() : float
    {
        return $this->_longitude;
    }

    public function getLongitudeInRadians() : float
    {
        return deg2rad($this->_longitude);
    }

    /**
     * @throws \Exception
     */
    public function setLongitude(float $longitude)
    {
        if ($longitude < -180.0 || $longitude > 180.0) {
            throw new \Exception();
        }

        $this->_longitude = $longitude;
        return $this;
    }

    public function asString(bool $reverseOrder = false, string $delimiter = ',') : string
    {
        $coords = [
            $this->getLongitude(),
            $this->getLatitude()
        ];

        if ($reverseOrder) {
            $coords = array_reverse($coords);
        }

        return join($delimiter, $coords);
    }
}