# geo
Direct &amp; inverse geodesic problems.

## Install via Composer

`composer require team-a/geo:1.0.0`

## Examples

```php
$Tver      = new TeamA\Point(56.8598, 35.8948);
$SouthPole = new TeamA\Point(-90.0, 0);

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

    

