<?php

namespace TeamA\Geo;

class Line
{
    public const POINT1 = 1;
    public const POINT2 = 2;

    /**
     * @var Point
     */
    protected $_point1;

    /**
     * @var Point
     */
    protected $_point2;

    public function __construct(Point $point1, Point $point2)
    {
        $this->setPoints($point1, $point2);
    }

    /**
     * Direct geodesic problem.
     *
     * @throws \Exception
     */
    public static function createByBearing(Point $startPoint, float $bearing, float $distance) : Line
    {
        return new Line(
            $startPoint,
            Point::createByBearing($startPoint, $bearing, $distance)
        );
    }

    public function setPoints(Point $point1 = null, Point $point2 = null) : self
    {
        if ($point1 !== null) {
            $this->_point1 = $point1;
        }

        if ($point2 !== null) {
            $this->_point2 = $point2;
        }

        return $this;
    }

    /**
     * @return Point[] with index 1, 2
     */
    public function getPoints() : array
    {
        return [
            self::POINT1 => $this->_point1,
            self::POINT2 => $this->_point2
        ];
    }

    public function getPoint1() : Point
    {
        return $this->_point1;
    }

    public function getPoint2() : Point
    {
        return $this->_point2;
    }

    /**
     * Inverse geodetic problem.
     */
    public function getDistance() : float // distance in meters
    {
        $lat1 = $this->_point1->getLatitudeInRadians();
        $lat2 = $this->_point2->getLatitudeInRadians();

        $long1 = $this->_point1->getLongitudeInRadians();
        $long2 = $this->_point2->getLongitudeInRadians();

        $cl1 = cos($lat1);
        $cl2 = cos($lat2);

        $sl1 = sin($lat1);
        $sl2 = sin($lat2);

        $delta = $long2 - $long1;

        $sdelta = sin($delta);
        $cdelta = cos($delta);

        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

        $ad = atan2($y, $x);

        return Point::EARTH_RADIUS * $ad;
    }

    /**
     * Inverse geodetic problem.
     */
    public function getInitialBearing(int $fromPoint = self::POINT1 /* one of self::POINT* */) : float //  bearing
    {
        $p1 = $this->_point1;
        $p2 = $this->_point2;

        if ($fromPoint == self::POINT2) {
            $this->_swap($p1, $p2);
        }

        $lat1 = $p1->getLatitudeInRadians();
        $lat2 = $p2->getLatitudeInRadians();

        $long1 = $p1->getLongitudeInRadians();
        $long2 = $p2->getLongitudeInRadians();

        $cl1 = cos($lat1);
        $cl2 = cos($lat2);

        $sl1 = sin($lat1);
        $sl2 = sin($lat2);

        $delta = $long2 - $long1;

        $sdelta = sin($delta);
        $cdelta = cos($delta);

        $y = $sdelta * $cl2;
        $x = $cl1 * $sl2 - $sl1 * $cl2 * $cdelta;

        $bearing = rad2deg(atan2($y, $x));
        $normalizeBearing = $bearing >= 0 ? $bearing : $bearing + 360;

        return $normalizeBearing;
    }

    public function getFinalBearing(int $toPoint = self::POINT2 /* one of self::POINT* */) : float // bearing
    {
        $bearing = $this->getInitialBearing($toPoint) + 180;

        return $bearing >= 360 ? $bearing - 360 : $bearing;
    }

    public function getMidPoint() : Point
    {
        $lat1 = $this->_point1->getLatitudeInRadians();
        $lat2 = $this->_point2->getLatitudeInRadians();

        $long1 = $this->_point1->getLongitudeInRadians();
        $long2 = $this->_point2->getLongitudeInRadians();

        $delta = $long2 - $long1;

        $Bx = cos($lat2) * cos($delta);
        $By = cos($lat2) * sin($delta);

        $lat = atan2(sin($lat1) + sin($lat2), sqrt(pow(cos($lat1) + $Bx, 2) + pow($By, 2)));
        $long = $long1 + atan2($By, cos($lat1) + $Bx);

        return Point::create(rad2deg($long), rad2deg($lat));
    }

    /**
     * @param mixed $v1
     * @param mixed $v2
     */
    protected function _swap(&$v1, &$v2)
    {
        $tmp = $v1;
        $v1 = $v2;
        $v2 = $tmp;
    }
}