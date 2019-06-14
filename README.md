# geo
Direct &amp; inverse geodesic problems.

The library uses a simple spherical model, not a geoid or even an ellipsoid. The error here will be about 0.06% at full Meridian and about 0.1% at half equator. On smaller segments the error is much smaller and practically suitable for tasks similar to logistics.

## Requirements

- php >= 7.1

## Install via Composer

`composer require team-a/geo:^1.0.0`

## Examples

```php
$Tver      = TeamA\Point::create(56.8598, 35.8948);
$SouthPole = TeamA\Point::create(-90.0, 0);

$distance = (
    new TeamA\Line($Tver, $SouthPole)
)
    ->getDistance()
;

$point2ToSouthPoleLine = TeamA\Line::createByBearing($Tver, 90.0, 1000.0);

$initialBearingToSouthPole = $point2ToSouthPoleLine->getInitialBearing();
$finalBearingToSouthPole   = $point2ToSouthPoleLine->getFinalBearing();

$midPoint = $point2ToSouthPoleLine->getMidPoint();
```

    

