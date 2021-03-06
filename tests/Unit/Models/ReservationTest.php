<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Guest;
use App\Models\GuestDetail;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReservationTest
 * @package Tests\Unit\Models
 * @group Unit
 * @group Unit.Models
 */
class ReservationTest extends BaseTestCase
{
    protected function getTarget(): string
    {
        return 'reservation';
    }

    /** @var Reservation $reservation */
    protected static $reservation;

    /** @var ReservationDetail $detail */
    protected static $detail;

    /** @var Guest $guest */
    protected static $guest;

    /** @var Room $room */
    protected static $room;

    protected static function seeder(): void
    {
        self::$guest = factory(Guest::class)->create();
        factory(GuestDetail::class)->create([
            'guest_id' => self::$guest->id,
        ]);
        self::$room        = factory(Room::class)->create();
        self::$reservation = factory(Reservation::class)->create([
            'guest_id' => self::$guest->id,
            'room_id'  => self::$room->id,
        ]);
        self::$detail      = factory(ReservationDetail::class)->create([
            'reservation_id' => self::$reservation->id,
            'payment'        => self::$reservation->room->price,
        ]);
    }

    public function hasOneDataProvider(): array
    {
        return [
            [ReservationDetail::class, 'detail'],
        ];
    }

    public function belongToDataProvider(): array
    {
        return [
            [Guest::class, 'guest'],
            [Room::class, 'room'],
        ];
    }

    public function testAttributes1()
    {
        $reservation = factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => date('Y-m-d'),
            'end_date'   => date('Y-m-d'),
        ]);
        static::runSeed([
            '--class' => 'SettingTableSeeder',
        ]);

        Reservation::setNow(strtotime(date('Y-m-d 14:59:59')));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertFalse(Reservation::find($reservation->id)->is_present);
        $this->assertTrue(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 15:00:00')));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertTrue(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 10:00:00').' +1days'));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertTrue(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 10:00:01').' +1days'));
        $this->assertTrue(Reservation::find($reservation->id)->is_past);
        $this->assertFalse(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(null);
    }

    public function testAttributes2()
    {
        $reservation = factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => date('Y-m-d'),
            'end_date'   => date('Y-m-d'),
            'checkout'   => '12:00:00',
        ]);
        static::runSeed([
            '--class' => 'SettingTableSeeder',
        ]);

        Reservation::setNow(strtotime(date('Y-m-d 14:59:59')));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertFalse(Reservation::find($reservation->id)->is_present);
        $this->assertTrue(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 15:00:00')));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertTrue(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 10:00:00').' +1days'));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertTrue(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 10:00:01').' +1days'));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertTrue(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 12:00:00').' +1days'));
        $this->assertFalse(Reservation::find($reservation->id)->is_past);
        $this->assertTrue(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(strtotime(date('Y-m-d 12:00:01').' +1days'));
        $this->assertTrue(Reservation::find($reservation->id)->is_past);
        $this->assertFalse(Reservation::find($reservation->id)->is_present);
        $this->assertFalse(Reservation::find($reservation->id)->is_future);

        Reservation::setNow(null);
    }

    public function testIsTermValid1()
    {
        Reservation::all()->each(function ($row) {
            /** @var Model $row */
            $row->delete();
        });
        Setting::create([
            'key'   => 'max_day',
            'value' => '4',
            'type'  => 'int',
        ]);
        Setting::clearCache();

        $day1 = now()->format('Y-m-d');
        $day2 = now()->addDay()->format('Y-m-d');
        $day3 = now()->addDays(2)->format('Y-m-d');
        $day4 = now()->addDays(3)->format('Y-m-d');
        $day5 = now()->addDays(4)->format('Y-m-d');
        factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => $day3,
            'end_date'   => $day4,
        ]);

        $this->assertTrue(Reservation::isTermValid($day1, $day1, null));
        $this->assertTrue(Reservation::isTermValid($day1, $day4, null));
        $this->assertFalse(Reservation::isTermValid($day1, $day5, null));
        $this->assertFalse(Reservation::isTermValid($day2, $day1, null));

        $this->assertTrue(Reservation::isTermValid(null, $day1, null));
        $this->assertTrue(Reservation::isTermValid($day1, null, null));
    }

    public function testIsTermValid2()
    {
        Reservation::all()->each(function ($row) {
            /** @var Model $row */
            $row->delete();
        });
        Setting::create([
            'key'   => 'max_day',
            'value' => '0',
            'type'  => 'int',
        ]);
        Setting::clearCache();

        $day1 = now()->format('Y-m-d');
        $day2 = now()->addDay()->format('Y-m-d');
        $day3 = now()->addDays(2)->format('Y-m-d');
        $day4 = now()->addDays(3)->format('Y-m-d');
        $day5 = now()->addDays(4)->format('Y-m-d');
        factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => $day3,
            'end_date'   => $day4,
        ]);

        $this->assertTrue(Reservation::isTermValid($day1, $day1, null));
        $this->assertTrue(Reservation::isTermValid($day1, $day4, null));
        $this->assertTrue(Reservation::isTermValid($day1, $day5, null));
        $this->assertFalse(Reservation::isTermValid($day2, $day1, null));
    }

    public function testIsTermValid3()
    {
        Reservation::all()->each(function ($row) {
            /** @var Model $row */
            $row->delete();
        });
        Setting::create([
            'key'   => 'max_day',
            'value' => '0',
            'type'  => 'int',
        ]);
        Setting::create([
            'key'   => 'checkin',
            'value' => '15:00',
            'type'  => 'time',
        ]);
        Setting::clearCache();

        $day1 = now()->format('Y-m-d');
        $day2 = now()->addDays(2)->format('Y-m-d');
        $day3 = now()->addDays(3)->format('Y-m-d');
        factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => $day2,
            'end_date'   => $day3,
        ]);

        $this->assertTrue(Reservation::isTermValid($day1, $day1, null));
        $this->assertTrue(Reservation::isTermValid($day1, $day1, '15:00'));
        $this->assertFalse(Reservation::isTermValid($day1, $day1, '15:01'));
    }

    public function testIsReservationAvailable()
    {
        Reservation::all()->each(function ($row) {
            /** @var Model $row */
            $row->delete();
        });

        $day1        = now()->format('Y-m-d');
        $day2        = now()->addDay()->format('Y-m-d');
        $day3        = now()->addDays(2)->format('Y-m-d');
        $day4        = now()->addDays(3)->format('Y-m-d');
        $day5        = now()->addDays(4)->format('Y-m-d');
        $day6        = now()->addDays(5)->format('Y-m-d');
        $reservation = factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => $day3,
            'end_date'   => $day4,
        ]);
        $room        = factory(Room::class)->create();

        $this->assertTrue(Reservation::isReservationAvailable(null, self::$room->id, $day1, $day2));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day1, $day2));

        $this->assertFalse(Reservation::isReservationAvailable(null, self::$room->id, $day2, $day3));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day2, $day3));

        $this->assertFalse(Reservation::isReservationAvailable(null, self::$room->id, $day3, $day4));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day3, $day4));

        $this->assertFalse(Reservation::isReservationAvailable(null, self::$room->id, $day4, $day5));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day4, $day5));

        $this->assertTrue(Reservation::isReservationAvailable(null, self::$room->id, $day5, $day6));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day5, $day6));

        $this->assertFalse(Reservation::isReservationAvailable(null, self::$room->id, $day2, $day5));
        $this->assertTrue(Reservation::isReservationAvailable($reservation->id, self::$room->id, $day2, $day5));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day2, $day5));

        $this->assertTrue(Reservation::isReservationAvailable(null, null, $day1, $day1));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, null, $day1));
        $this->assertTrue(Reservation::isReservationAvailable(null, $room->id, $day1, null));
    }

    public function testIsNotDuplicated()
    {
        Reservation::all()->each(function ($row) {
            /** @var Model $row */
            $row->delete();
        });

        $day1        = now()->format('Y-m-d');
        $day2        = now()->addDay()->format('Y-m-d');
        $day3        = now()->addDays(2)->format('Y-m-d');
        $day4        = now()->addDays(3)->format('Y-m-d');
        $day5        = now()->addDays(4)->format('Y-m-d');
        $day6        = now()->addDays(5)->format('Y-m-d');
        $reservation = factory(Reservation::class)->create([
            'guest_id'   => self::$guest->id,
            'room_id'    => self::$room->id,
            'start_date' => $day3,
            'end_date'   => $day4,
        ]);

        $this->assertTrue(Reservation::isNotDuplicated(null, self::$guest->id, $day1, $day2));

        $this->assertFalse(Reservation::isNotDuplicated(null, self::$guest->id, $day2, $day3));

        $this->assertFalse(Reservation::isNotDuplicated(null, self::$guest->id, $day3, $day4));

        $this->assertFalse(Reservation::isNotDuplicated(null, self::$guest->id, $day4, $day5));

        $this->assertTrue(Reservation::isNotDuplicated(null, self::$guest->id, $day5, $day6));

        $this->assertFalse(Reservation::isNotDuplicated(null, self::$guest->id, $day2, $day5));
        $this->assertTrue(Reservation::isNotDuplicated($reservation->id, self::$guest->id, $day2, $day5));

        $this->assertTrue(Reservation::isNotDuplicated(null, null, $day1, $day1));
        $this->assertTrue(Reservation::isNotDuplicated(null, self::$guest->id, null, $day1));
        $this->assertTrue(Reservation::isNotDuplicated(null, self::$guest->id, $day1, null));
    }
}
