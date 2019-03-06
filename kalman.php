<?php
class KalmanFilter {

    private $timeStamp; // millis
    private $latitude; // degree
    private $longitude; // degree
    private $variance; // P matrix. Initial estimate of error


    public function __construct() {
        $this->variance = -1;
    }

    // Init method (use this after constructor, and before process)
    // if you are using last known data from gps)
    public function setState($latitude, $longitude, $timeStamp, $accuracy) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timeStamp = $timeStamp;
        $this->variance = $accuracy * $accuracy;
    }

    /**
     * Kalman filter processing for latitude and longitude
     *
     * newLatitude - new measurement of latitude
     * newLongitude - new measurement of longitude
     * accuracy - measurement of 1 standard deviation error in meters
     * newTimeStamp - time of measurement in millis
     */
    public function process($speed, $lat, $long, $time, $acc) {
        // Uncomment this, if you are receiving accuracy from your gps
        // if (newAccuracy < Constants.MIN_ACCURACY) {
        //      newAccuracy = Constants.MIN_ACCURACY;
        // }
        if ($this->variance < 0) {
        $this->setState($lat, $long, $time, $acc);
        return [$lat, $long];
        } else {
            // else apply Kalman filter
            $duration = $time - $this->timeStamp;
            if ($duration > 0) {
                // time has moved on, so the uncertainty in the current position increases
                $this->variance += $duration * $speed * $speed / 1000;
                $this->timeStamp = $time;
            }

            // Kalman gain matrix 'k' = Covariance * Inverse(Covariance + MeasurementVariance)
            // because 'k' is dimensionless,
            // it doesn't matter that variance has different units to latitude and longitude
            $k = $this->variance / ($this->variance + $acc * $acc);
            // apply 'k'
            $this->latitude += $k * ($lat - $this->latitude);
            $this->longitude += $k * ($long - $this->longitude);
            // new Covariance matrix is (IdentityMatrix - k) * Covariance
            $this->variance = (1 - $k) * $this->variance;

            // Export new point
            return [$this->latitude, $this->longitude];
        }
    }
}

    
