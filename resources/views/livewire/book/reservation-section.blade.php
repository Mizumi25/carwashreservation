<?php

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Service;
use App\Models\Package;
use App\Models\Schedule; 
use App\Models\User; 
use App\Models\Reservation;
use Livewire\Volt\Component;
use Carbon\Carbon; 
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Str;

new class extends Component
{
    public $vehicleTypes;
    public $service;
    public $service1;
    public $service2;
    public $service3;
    public $package;
    public $selectedServiceId = null;
    public $selectedServiceId1 = null;
    public $selectedServiceId2 = null;
    public $selectedServiceId3 = null;
    public $selectedPackageId = null;
    public $selectedPackageId1 = null;
    public $selectedPackageId2 = null;
    public $selectedPackageId3 = null;
    public $selectedVehicleTypeId = null;
    public $selectedVehicleTypeId1 = null;
    public $selectedVehicleTypeId2 = null;
    public $selectedVehicleTypeId3 = null;
    public $model = '';
    public $model1 = '';
    public $model2 = '';
    public $model3 = '';
    public $make = '';
    public $make1 = '';
    public $make2 = '';
    public $make3 = '';
    public $year = '';
    public $year1 = '';
    public $year2 = '';
    public $year3 = '';
    public $license_plate = '';
    public $license_plate1 = '';
    public $license_plate2 = '';
    public $license_plate3 = '';
    public $color = '';
    public $color1 = '';
    public $color2 = '';
    public $color3 = '';
    public $currentDay;
    public $days = [];
    public $availableTimes;
    public $selectedDate;
    public $selectedDate1;
    public $selectedDate2;
    public $selectedDate3;
    public $selectedTime;
    public $selectedTime1;
    public $selectedTime2;
    public $selectedTime3;
    public $isAgreed = false;
    public $vehicles;
    public $selectedVehicleId = null;
    public $selectedVehicleId1 = null;
    public $selectedVehicleId2 = null;
    public $selectedVehicleId3 = null;
    public $isVehicleInputEnabled = false;
    public $isVehicleInputEnabled1 = false;
    public $isVehicleInputEnabled2 = false;
    public $isVehicleInputEnabled3 = false;
    public $licensePlateError = '';
    public $disabledSchedules;
    
    public $packageImages = [
      'package1.png',
      'package2.png',
      'package3.png',
    ];

    
    public function mount(): void 
    {
        $this->vehicles = Vehicle::where('user_id', auth()->id())
        ->select('id', 'make', 'model')
        ->get() ?? collect(); 
        $this->vehicleTypes = VehicleType::select('id', 'name', 'description', 'price', 'icon')->get() ?? collect(); 
        $this->service = Service::select('id', 'service_name', 'icon', 'description', 'price', 'duration', 'is_active', 'category_id', 'popularity')
            ->where('is_active', true)
            ->get();
        $this->service1 = $this->service;
        $this->service2 = $this->service;
        $this->service3 = $this->service;
        $this->package = Package::with('services')->get();  
        $this->currentDay = Carbon::now();
        $this->generateDays();
        $this->availableTimes = [
            '08:00 am', '09:00 am', '10:00 am',
            '11:00 am', '12:00 pm', '01:00 pm',
            '02:00 pm', '03:00 pm', '04:00 pm',
            '05:00 pm'
        ];
        
        $this->disabledSchedules = collect(); 
        $this->fetchDisabledSchedules();
        
        $this->packageImages = array_map(function($image) {
            return asset('storage/package_icons/' . $image);
        }, $this->packageImages);
    }
    
    public function updatedSelectedVehicleId($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $this->make = $vehicle->make;
            $this->model = $vehicle->model;
            $this->year = $vehicle->year;
            $this->license_plate = $vehicle->license_plate;
            $this->color = $vehicle->color;
            $this->selectedVehicleTypeId = $vehicle->vehicle_type_id; 
            $this->isVehicleInputEnabled = true; 
        }
    }

    public function updatedSelectedVehicleId1($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $this->make1 = $vehicle->make;
            $this->model1 = $vehicle->model;
            $this->year1 = $vehicle->year;
            $this->license_plate1 = $vehicle->license_plate;
            $this->color1 = $vehicle->color;
            $this->selectedVehicleTypeId1 = $vehicle->vehicle_type_id; 
            $this->isVehicleInputEnabled1 = true; 
        }
    }

    public function updatedSelectedVehicleId2($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $this->make2 = $vehicle->make;
            $this->model2 = $vehicle->model;
            $this->year2 = $vehicle->year;
            $this->license_plate2 = $vehicle->license_plate;
            $this->color2 = $vehicle->color;
            $this->selectedVehicleTypeId2 = $vehicle->vehicle_type_id; 
            $this->isVehicleInputEnabled2 = true; 
        }
    }

    public function updatedSelectedVehicleId3($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $this->make3 = $vehicle->make;
            $this->model3 = $vehicle->model;
            $this->year3 = $vehicle->year;
            $this->license_plate3 = $vehicle->license_plate;
            $this->color3 = $vehicle->color;
            $this->selectedVehicleTypeId3 = $vehicle->vehicle_type_id; 
            $this->isVehicleInputEnabled3 = true; 
        }
    }


    
    
    public function fetchDisabledSchedules()
    {
        $ongoingReservations = Reservation::whereIn('status', ['ongoing', 'in_progress'])->with('schedule')->get();
        
        $this->disabledSchedules = collect(); 
    
        foreach ($ongoingReservations as $reservation) {
            $scheduleDate = Carbon::parse($reservation->schedule->date);
            
            $this->disabledSchedules->push([
                'date' => $scheduleDate->toDateString(),
                'time_slot' => Carbon::parse($reservation->schedule->time_slot)->format('h:i A'), 
            ]);
        }
    }
    


    public function selectVehicleType($vehicleTypeId, $cardNumber)
    {
        if ($cardNumber === 1) {
            $this->selectedVehicleTypeId = $vehicleTypeId; 
            $vehicleType = $this->vehicleTypes->find($vehicleTypeId);
            if ($vehicleType) {
                $this->vehicleTypeName = $vehicleType->name; 
                $this->vehicleTypePrice = $vehicleType->price; 
                $this->isVehicleInputEnabled = true; 
            }
        } else if ($cardNumber === 2) {
            $this->selectedVehicleTypeId1 = $vehicleTypeId; 
            $vehicleType = $this->vehicleTypes->find($vehicleTypeId);
            if ($vehicleType) {
                $this->vehicleTypeName1 = $vehicleType->name; 
                $this->vehicleTypePrice1 = $vehicleType->price; 
                $this->isVehicleInputEnabled1 = true; 
            }
        } else if ($cardNumber === 3) {
            $this->selectedVehicleTypeId2 = $vehicleTypeId; 
            $vehicleType = $this->vehicleTypes->find($vehicleTypeId);
            if ($vehicleType) {
                $this->vehicleTypeName2 = $vehicleType->name; 
                $this->vehicleTypePrice2 = $vehicleType->price; 
                $this->isVehicleInputEnabled2 = true; 
            }
        } else if ($cardNumber === 4) {
            $this->selectedVehicleTypeId3 = $vehicleTypeId; 
            $vehicleType = $this->vehicleTypes->find($vehicleTypeId);
            if ($vehicleType) {
                $this->vehicleTypeName3 = $vehicleType->name; 
                $this->vehicleTypePrice3 = $vehicleType->price; 
                $this->isVehicleInputEnabled3 = true; 
            }
        }
    }

    
    
    public function selectService($serviceId, $cardNumber)
    {
        if ($cardNumber === 1) {
            $this->selectedPackageId = null;
            $this->selectedServiceId = $serviceId;
        } else if ($cardNumber === 2) {
            $this->selectedPackageId1 = null;
            $this->selectedServiceId1 = $serviceId;
        } else if ($cardNumber === 3) {
            $this->selectedPackageId2 = null;
            $this->selectedServiceId2 = $serviceId;
        } else if ($cardNumber === 4) {
            $this->selectedPackageId3 = null;
            $this->selectedServiceId3 = $serviceId;
        }
    }
    
    public function selectPackage($packageId, $cardNumber)
    {
        if ($cardNumber === 1) {
            $this->selectedServiceId = null;
            $this->selectedPackageId = $packageId;
        } else if ($cardNumber === 2) {
            $this->selectedServiceId1 = null;
            $this->selectedPackageId1 = $packageId;
        } else if ($cardNumber === 3) {
            $this->selectedServiceId2 = null;
            $this->selectedPackageId2 = $packageId;
        } else if ($cardNumber === 4) {
            $this->selectedServiceId3 = null;
            $this->selectedPackageId3 = $packageId;
        }
    }
    
    
    public function getTotalPriceProperty()
    {
        $servicePrice = optional($this->service->find($this->selectedServiceId))->price ?? 0;
        
        $package = $this->package->find($this->selectedPackageId);
        $packagePrice = optional($package)->original_price ?? 0;
        
        if ($package) {
            $discountPercentage = optional($package)->discount ?? 0; 
            $discountAmount = ($packagePrice * $discountPercentage) / 100; 
            $packagePrice -= $discountAmount; 
        }
    
        $vehicleTypePrice = optional($this->vehicleTypes->find($this->selectedVehicleTypeId))->price ?? 0;
    
        return $servicePrice + $packagePrice + $vehicleTypePrice; 
    }

    public function getTotalPrice1Property()
    {
        $servicePrice = optional($this->service->find($this->selectedServiceId1))->price ?? 0;
        
        $package = $this->package->find($this->selectedPackageId1);
        $packagePrice = optional($package)->original_price ?? 0;
        
        if ($package) {
            $discountPercentage = optional($package)->discount ?? 0; 
            $discountAmount = ($packagePrice * $discountPercentage) / 100; 
            $packagePrice -= $discountAmount; 
        }
    
        $vehicleTypePrice = optional($this->vehicleTypes->find($this->selectedVehicleTypeId1))->price ?? 0;
    
        return $servicePrice + $packagePrice + $vehicleTypePrice; 
    }

    public function getTotalPrice2Property()
    {
        $servicePrice = optional($this->service->find($this->selectedServiceId2))->price ?? 0;
        
        $package = $this->package->find($this->selectedPackageId2);
        $packagePrice = optional($package)->original_price ?? 0;
        
        if ($package) {
            $discountPercentage = optional($package)->discount ?? 0; 
            $discountAmount = ($packagePrice * $discountPercentage) / 100; 
            $packagePrice -= $discountAmount; 
        }
    
        $vehicleTypePrice = optional($this->vehicleTypes->find($this->selectedVehicleTypeId2))->price ?? 0;
    
        return $servicePrice + $packagePrice + $vehicleTypePrice; 
    }

    public function getTotalPrice3Property()
    {
        $servicePrice = optional($this->service->find($this->selectedServiceId3))->price ?? 0;
        
        $package = $this->package->find($this->selectedPackageId3);
        $packagePrice = optional($package)->original_price ?? 0;
        
        if ($package) {
            $discountPercentage = optional($package)->discount ?? 0; 
            $discountAmount = ($packagePrice * $discountPercentage) / 100; 
            $packagePrice -= $discountAmount; 
        }
    
        $vehicleTypePrice = optional($this->vehicleTypes->find($this->selectedVehicleTypeId3))->price ?? 0;
    
        return $servicePrice + $packagePrice + $vehicleTypePrice; 
    }




    public function clearCardInputs($cardNumber)
    {
        if ($cardNumber == 1) {
            $this->selectedVehicleTypeId = null;
            $this->model = '';
            $this->make = '';
            $this->year = '';
            $this->license_plate = '';
            $this->color = '';
            $this->selectedVehicleId = null;
            $this->isVehicleInputEnabled = false;
            $this->selectedServiceId = null;
            $this->selectedPackageId = null;
            $this->selectedDate = null;
            $this->selectedTime = null;
        } elseif ($cardNumber == 2) {
            $this->selectedVehicleTypeId1 = null;
            $this->model1 = '';
            $this->make1 = '';
            $this->year1 = '';
            $this->license_plate1 = '';
            $this->color1 = '';
            $this->selectedVehicleId1 = null;
            $this->isVehicleInputEnabled1 = false;
            $this->selectedServiceId1 = null;
            $this->selectedPackageId1 = null;
            $this->selectedDate1 = null;
            $this->selectedTime1 = null;
        } elseif ($cardNumber == 3) {
            $this->selectedVehicleTypeId2 = null;
            $this->model2 = '';
            $this->make2 = '';
            $this->year2 = '';
            $this->license_plate2 = '';
            $this->color2 = '';
            $this->selectedVehicleId2 = null;
            $this->isVehicleInputEnabled2 = false;
            $this->selectedServiceId2 = null;
            $this->selectedPackageId2 = null;
            $this->selectedDate2 = null;
            $this->selectedTime2 = null;
        } elseif ($cardNumber == 4) {
            $this->selectedVehicleTypeId3 = null;
            $this->model3 = '';
            $this->make3 = '';
            $this->year3 = '';
            $this->license_plate3 = '';
            $this->color3 = '';
            $this->selectedVehicleId3 = null;
            $this->isVehicleInputEnabled3 = false;
            $this->selectedServiceId3 = null;
            $this->selectedPackageId3 = null;
            $this->selectedDate3 = null;
            $this->selectedTime3 = null;
        }
    }



public function submitReserve()
    {
        $this->licensePlateError = null;
        $rulesCard1 = [
            'selectedVehicleTypeId' => 'required|exists:vehicle_types,id',
            'model' => 'required|string',
            'make' => 'required|string',
            'year' => 'required|integer',
            'license_plate' => 'required|string',
            'color' => 'required|string',
            'selectedServiceId' => 'nullable|exists:services,id',
            'selectedPackageId' => 'nullable|exists:packages,id',
            'selectedDate' => 'required|date',
            'selectedTime' => 'required|string',
        ];

        $rulesCard2 = [
            'selectedVehicleTypeId1' => 'required|exists:vehicle_types,id',
            'model1' => 'required|string',
            'make1' => 'required|string',
            'year1' => 'required|integer',
            'license_plate1' => 'required|string',
            'color1' => 'required|string',
            'selectedServiceId1' => 'nullable|exists:services,id',
            'selectedPackageId1' => 'nullable|exists:packages,id',
            'selectedDate1' => 'required|date',
            'selectedTime1' => 'required|string',
        ];

        $rulesCard3 = [
            'selectedVehicleTypeId2' => 'required|exists:vehicle_types,id',
            'model2' => 'required|string',
            'make2' => 'required|string',
            'year2' => 'required|integer',
            'license_plate2' => 'required|string',
            'color2' => 'required|string',
            'selectedServiceId2' => 'nullable|exists:services,id',
            'selectedPackageId2' => 'nullable|exists:packages,id',
            'selectedDate2' => 'required|date',
            'selectedTime2' => 'required|string',
        ];

        $rulesCard4 = [
            'selectedVehicleTypeId3' => 'required|exists:vehicle_types,id',
            'model3' => 'required|string',
            'make3' => 'required|string',
            'year3' => 'required|integer',
            'license_plate3' => 'required|string',
            'color3' => 'required|string',
            'selectedServiceId3' => 'nullable|exists:services,id',
            'selectedPackageId3' => 'nullable|exists:packages,id',
            'selectedDate3' => 'required|date',
            'selectedTime3' => 'required|string',
        ];
        
        $isCard1Filled = $this->model 
            && $this->make 
            && $this->year 
            && $this->license_plate 
            && $this->color 
            && $this->selectedDate 
            && $this->selectedTime 
            && ($this->selectedPackageId || $this->selectedServiceId) 
            && $this->selectedVehicleTypeId;

        $isCard2Filled = $this->model1 
            && $this->make1 
            && $this->year1 
            && $this->license_plate1 
            && $this->color1 
            && $this->selectedDate1 
            && $this->selectedTime1 
            && ($this->selectedPackageId1 || $this->selectedServiceId1) 
            && $this->selectedVehicleTypeId1;

        $isCard3Filled = $this->model2
            && $this->make2 
            && $this->year2
            && $this->license_plate2 
            && $this->color2 
            && $this->selectedDate2
            && $this->selectedTime2 
            && ($this->selectedPackageId2 || $this->selectedServiceId2) 
            && $this->selectedVehicleTypeId2;
        
        $isCard4Filled = $this->model3
            && $this->make3 
            && $this->year3
            && $this->license_plate3 
            && $this->color3 
            && $this->selectedDate3
            && $this->selectedTime3 
            && ($this->selectedPackageId3 || $this->selectedServiceId3) 
            && $this->selectedVehicleTypeId3;

        $successfulReservations = 0;

        if ($isCard1Filled) {
            $this->validate($rulesCard1);
            
            if ($this->processCard(1)) {
                $successfulReservations++;
            }
        }

        if ($isCard2Filled) {
            $this->validate($rulesCard2);
            
            if ($this->processCard(2)) {
                $successfulReservations++;
            }
        }

        if ($isCard3Filled) {
            $this->validate($rulesCard3);
            
            if ($this->processCard(3)) {
                $successfulReservations++;
            }
        }

        if ($isCard4Filled) {
            $this->validate($rulesCard4);
            
            if ($this->processCard(4)) {
                $successfulReservations++;
            }
        }

        if ($successfulReservations === 0) {
            if ($this->licensePlateError) {
                session()->flash('error', $this->licensePlateError);
            } else {
                session()->flash('error', 'Failed to create reservations. Please check the details and try again.');
            }
            return; 
        }

        Toaster::success('Request submitted successfully, please wait for updates.');
        session()->flash('message', 'Service reserved successfully!');
        $this->reset();

        return redirect()->route('reservations.manage');
    }


    private function handleVehicle($make, $model, $year, $licensePlate, $color, $vehicleTypeId)
    {
        $existingVehicle = Vehicle::where('make', $make)
            ->where('vehicle_type_id', $vehicleTypeId)
            ->where('model', $model)
            ->where('year', $year)
            ->where('color', $color)
            ->where('license_plate', $licensePlate)
            ->where('user_id', auth()->id())
            ->first();
    
        if ($existingVehicle) {
            return $existingVehicle->id; 
        }
    
        $licensePlateExists = Vehicle::where('license_plate', $licensePlate)
            ->where('user_id', auth()->id())
            ->exists();
    
        if ($licensePlateExists) {
            $this->licensePlateError = 'This license plate is already in use. Please use a unique license plate.';
            return false; 
        }
    
        $vehicle = Vehicle::create([
            'user_id' => auth()->id(),
            'vehicle_type_id' => $vehicleTypeId,
            'model' => $model,
            'make' => $make,
            'year' => $year,
            'license_plate' => $licensePlate,
            'color' => $color,
        ]);
    
        return $vehicle->id; 
    }
    


private function processCard($cardNumber): bool
{
    if ($cardNumber == 1) {
        $vehicleId = $this->handleVehicle(
            $this->make, $this->model, $this->year, $this->license_plate, $this->color, $this->selectedVehicleTypeId
        );

        if ($this->licensePlateError) {
            session()->flash('error', $this->licensePlateError);
            return false;
        }

        if (!$vehicleId) {
            session()->flash('error', 'Failed to create or retrieve vehicle. Please check the details.');
            return false;
        }

        $schedule = Schedule::create([
            'date' => $this->selectedDate,
            'time_slot' => date('H:i', strtotime($this->selectedTime)),
        ]);

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $vehicleId,
            'service_id' => $this->selectedServiceId,
            'package_id' => $this->selectedPackageId,
            'schedule_id' => $schedule->id,
            'reservation_date' => now(),
            'status' => 'pending',
        ]);

        // Broadcast and notify admins
        $currentUser  = auth()->user();
        $service = Service::find($this->selectedServiceId);
        $package = Package::find($this->selectedPackageId); 
        $adminUsers = User::where('role', 'admin')->get(); 

        foreach ($adminUsers as $admin) {
            $eventServiceOrPackage = $service ?: $package; 
            
            broadcast(new \App\Events\ReservationCreate($reservation, $currentUser , $eventServiceOrPackage));

            $messageBody = 'A Reservation has been made with ID: ' . $reservation->id;

            if ($service) {
                $messageBody .= ' for Service: ' . $service->service_name;
            } elseif ($package) {
                $messageBody .= ' for Package: ' . $package->name;
            }
            
            Notification::make()
                ->title('New Reservation by ' . $currentUser->name)
                ->body($messageBody . '. View Pending Reservations Now.')
                ->actions([
                    Action::make('approve')
                        ->button() 
                        ->color('success') 
                        ->action(function () use ($reservation) {
                            $reservation->update(['status' => 'approve']);
                        
                            broadcast(new \App\Events\ReservationStatusUpdated($reservation));
                        }),
                    Action::make('decline')
                        ->button() 
                        ->color('danger') 
                        ->action(function () use ($reservation) {
                            $reservation->update(['status' => 'decline']);
                        }),
                ])
                ->sendToDatabase($admin); 
        }

        return true;
    } elseif ($cardNumber == 2) {
        $vehicleId1 = $this->handleVehicle(
            $this->make1, $this->model1, $this->year1, $this->license_plate1, $this->color1, $this->selectedVehicleTypeId1
        );

        if ($this->licensePlateError) {
            session()->flash('error', $this->licensePlateError);
            return false;
        }

        if (!$vehicleId1) {
            session()->flash('error', 'Failed to create or retrieve vehicle for Card 2. Please check the details.');
            return false;
        }

        $schedule1 = Schedule::create([
            'date' => $this->selectedDate1,
            'time_slot' => date('H:i', strtotime($this->selectedTime1)),
        ]);

        $reservation1 = Reservation::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $vehicleId1,
            'service_id' => $this->selectedServiceId1,
            'package_id' => $this->selectedPackageId1,
            'schedule_id' => $schedule1->id,
            'reservation_date' => now(),
            'status' => 'pending',
        ]);

        // Broadcast and notify admins
        $currentUser  = auth()->user();
        $service1 = Service::find($this->selectedServiceId1);
        $package1 = Package::find($this->selectedPackageId1); 
        $adminUsers = User::where('role', 'admin')->get(); 

        foreach ($adminUsers as $admin) {
            $eventServiceOrPackage1 = $service1 ?: $package1; 
            
            broadcast(new \App\Events\ReservationCreate($reservation1, $currentUser , $eventServiceOrPackage1));

            $messageBody1 = 'A Reservation has been made with ID: ' . $reservation1->id;

            if ($service1) {
                $messageBody1 .= ' for Service: ' . $service1->service_name;
            } elseif ($package1) {
                $messageBody1 .= ' for Package: ' . $package1->name;
            }
            
            Notification::make()
                ->title('New Reservation by ' . $currentUser->name)
                ->body($messageBody1 . '. View Pending Reservations Now.')
                ->actions([
                    Action::make('approve')
                        ->button() 
                        ->color('success') 
                        ->action(function () use ($reservation1) {
                            $reservation1->update(['status' => 'approve']);
                        
                            broadcast(new \App\Events\ReservationStatusUpdated($reservation1));
                        }),
                    Action::make('decline')
                        ->button() 
                        ->color('danger') 
                        ->action(function () use ($reservation1) {
                            $reservation1->update(['status' => 'decline']);
                        }),
                ])
                ->sendToDatabase($admin); 
        }

        return true;
    } elseif ($cardNumber == 3) {
        $vehicleId2 = $this->handleVehicle(
            $this->make2, $this->model2, $this->year2, $this->license_plate2, $this->color2, $this->selectedVehicleTypeId2
        );

        if ($this->licensePlateError) {
            session()->flash('error', $this->licensePlateError);
            return false;
        }

        if (!$vehicleId2) {
            session()->flash('error', 'Failed to create or retrieve vehicle for Card 3. Please check the details.');
            return false;
        }

        $schedule2 = Schedule::create([
            'date' => $this->selectedDate2,
            'time_slot' => date('H:i', strtotime($this->selectedTime2)),
        ]);

        $reservation2 = Reservation::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $vehicleId2,
            'service_id' => $this->selectedServiceId2,
            'package_id' => $this->selectedPackageId2,
            'schedule_id' => $schedule2->id,
            'reservation_date' => now(),
            'status' => 'pending',
        ]);

        // Broadcast and notify admins
        $currentUser  = auth()->user();
        $service2 = Service::find($this->selectedServiceId2);
        $package2 = Package::find($this->selectedPackageId2); 
        $adminUsers = User::where('role', 'admin')->get(); 

        foreach ($adminUsers as $admin) {
            $eventServiceOrPackage2 = $service2 ?: $package2; 
            
            broadcast(new \App\Events\ReservationCreate($reservation2, $currentUser , $eventServiceOrPackage2));

            $messageBody2 = 'A Reservation has been made with ID: ' . $reservation2->id;

            if ($service2) {
                $messageBody2 .= ' for Service: ' . $service2->service_name;
            } elseif ($package2) {
                $messageBody2 .= ' for Package: ' . $package2->name;
            }
            
            Notification::make()
                ->title('New Reservation by ' . $currentUser->name)
                ->body($messageBody2 . '. View Pending Reservations Now.')
                ->actions([
                    Action::make('approve')
                        ->button() 
                        ->color('success') 
                        ->action(function () use ($reservation2) {
                            $reservation1->update(['status' => 'approve']);
                        
                            broadcast(new \App\Events\ReservationStatusUpdated($reservation2));
                        }),
                    Action::make('decline')
                        ->button() 
                        ->color('danger') 
                        ->action(function () use ($reservation2) {
                            $reservation2->update(['status' => 'decline']);
                        }),
                ])
                ->sendToDatabase($admin); 
        }

        return true;
    } elseif ($cardNumber == 4) {
        $vehicleId3 = $this->handleVehicle(
            $this->make3, $this->model3, $this->year3, $this->license_plate3, $this->color3, $this->selectedVehicleTypeId3
        );

        if ($this->licensePlateError) {
            session()->flash('error', $this->licensePlateError);
            return false;
        }

        if (!$vehicleId3) {
            session()->flash('error', 'Failed to create or retrieve vehicle for Card 4. Please check the details.');
            return false;
        }

        $schedule3 = Schedule::create([
            'date' => $this->selectedDate3,
            'time_slot' => date('H:i', strtotime($this->selectedTime3)),
        ]);

        $reservation3 = Reservation::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $vehicleId3,
            'service_id' => $this->selectedServiceId3,
            'package_id' => $this->selectedPackageId3,
            'schedule_id' => $schedule3->id,
            'reservation_date' => now(),
            'status' => 'pending',
        ]);

        // Broadcast and notify admins
        $currentUser  = auth()->user();
        $service3 = Service::find($this->selectedServiceId3);
        $package3 = Package::find($this->selectedPackageId3); 
        $adminUsers = User::where('role', 'admin')->get(); 

        foreach ($adminUsers as $admin) {
            $eventServiceOrPackage3 = $service3 ?: $package3; 
            
            broadcast(new \App\Events\ReservationCreate($reservation3, $currentUser , $eventServiceOrPackage3));

            $messageBody3 = 'A Reservation has been made with ID: ' . $reservation3->id;

            if ($service3) {
                $messageBody3 .= ' for Service: ' . $service3->service_name;
            } elseif ($package3) {
                $messageBody3 .= ' for Package: ' . $package3->name;
            }
            
            Notification::make()
                ->title('New Reservation by ' . $currentUser->name)
                ->body($messageBody3 . '. View Pending Reservations Now.')
                ->actions([
                    Action::make('approve')
                        ->button() 
                        ->color('success') 
                        ->action(function () use ($reservation3) {
                            $reservation3->update(['status' => 'approve']);
                        
                            broadcast(new \App\Events\ReservationStatusUpdated($reservation3));
                        }),
                    Action::make('decline')
                        ->button() 
                        ->color('danger') 
                        ->action(function () use ($reservation3) {
                            $reservation3->update(['status' => 'decline']);
                        }),
                ])
                ->sendToDatabase($admin); 
        }

        return true;
    }

    return false;
}







    
    public function generateDays()
    {
        $this->days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $this->currentDay->copy()->addDays($i);
            $this->days[] = $day;
        }
    }

    public function goToNextDays()
    {
        $this->currentDay->addDays(3);
        $this->generateDays();
    }

    public function goToPreviousDays()
    {
        if ($this->currentDay->greaterThan(Carbon::now())) {
            $this->currentDay->subDays(3);
            $this->generateDays();
        }
    }

        

    public function selectDay($day, $cardNumber)
    {
        if ($cardNumber === 1) {
            $this->selectedDate = $day;
            $this->selectedTime = null;
        } else if ($cardNumber === 2) {
            $this->selectedDate1 = $day;
            $this->selectedTime1 = null;
        } else if ($cardNumber === 3) {
            $this->selectedDate2 = $day;
            $this->selectedTime2 = null;
        } else if ($cardNumber === 4) {
            $this->selectedDate3 = $day;
            $this->selectedTime3 = null;
        } 
    }

        

    public function selectTime($time, $cardNumber)
    {
        if ($cardNumber === 1) {
            if ($this->selectedDate) {
            $this->selectedTime = $time;
            }
        } else if ($cardNumber === 2) {
            if ($this->selectedDate1) {
            $this->selectedTime1 = $time;
            }
        } else if ($cardNumber === 3) {
            if ($this->selectedDate2) {
            $this->selectedTime2 = $time;
            }
        } else if ($cardNumber === 4) {
            if ($this->selectedDate3) {
            $this->selectedTime3 = $time;
            }
        }
    }
    
   public function getIsSubmitEnabledProperty()
  {
      return !empty($this->selectedVehicleTypeId) &&
             (!empty($this->selectedServiceId) || !empty($this->selectedPackageId)) && 
             !empty($this->model) &&
             !empty($this->make) &&
             !empty($this->year) &&
             !empty($this->license_plate) &&
             !empty($this->color) &&
             !empty($this->selectedDate) &&
             !empty($this->selectedTime);
  }
};
?>


            <div class="px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white/70 text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">


                <div x-data="{ 
                    open: false, 
                    selectedCard: 1, 
                    cards: [], 
                    showModal: true,
                    cardCount: 1
                }" 
                x-init="cards = [1];" 
                class="relative z-10"
                @click.outside="open = false"
                >

                    <!-- Modal for selecting number of cards -->
                    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
                        <div class="bg-white p-5 rounded-lg shadow-lg">
                            <h2 class="text-lg font-bold mb-4 text-wrap">How many reservations would you like?</h2>
                            <select x-model="cardCount" class="border p-2 mb-4 w-full">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                            <div class="flex justify-end">
                                <button 
                                    @click="showModal = false; cards = Array.from({length: cardCount}, (_, i) => i + 1);" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Overlay Toggle Button -->
                    <button 
                        id="overlaytoggle" 
                        type="button" 
                        class="fixed left-[15rem] bottom-[-20px] w-52 h-32 m-3 p-2 rounded-lg bg-white text-black flex justify-between cursor-pointer z-30 transition-colors duration-300"
                        @click="open = !open"
                        :class="{'bg-blue-500': open}">
                        <p>Multi reservation</p>
                        <p>^</p>
                    </button>

                    <div 
                        x-ref="sidereservation" 
                        class="fixed left-[15rem] top-[4rem] w-50 h-auto m-3 p-2 flex flex-col gap-3 overflow-hidden z-50"
                        :style="open ? 'transform: translateX(0);' : 'transform: translateX(100%); transition: transform 0.5s ease;'">
                        
                        <!-- Add Card Button -->
                        <button 
                            x-show="open && cards.length < 4" 
                            @click="let range = [1, 2, 3, 4]; let missing = range.find(num => !cards.includes(num)); if (missing !== undefined) { cards.push(missing); }" 
                            class="mb-2 p-2 rounded-lg bg-blue-500 text-white"
                            x-transition:enter="transition transform ease-out duration-300"
                            x-transition:enter-start="translate-x-[-10px] opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"
                            x-transition:leave="transition transform ease-in duration-300"
                            x-transition:leave-start="translate-x-0 opacity-100"
                            x-transition:leave-end="translate-x-[10px] opacity-0">
                            Add Reservation
                        </button>

                        <!-- Cards Display -->
                        <div class="flex flex-col gap-3">
                            <template x-for="(card, index) in cards" :key="card">
                                <div 
                                    class="relative w-48 h-32 bg-white text-black rounded-lg shadow-md transition-all duration-300 p-4 hover:bg-blue-200 hover:scale-105"
                                    x-show="open" 
                                    x-transition:enter="transition transform ease-out duration-300"
                                    x-transition:enter-start="translate-x-0 opacity-0"
                                    x-transition:enter-end="translate-x-10 opacity-100"
                                    x-transition:leave="transition transform ease-in duration-300"
                                    x-transition:leave-start="translate-y-0 opacity-100"
                                    x-transition:leave-end="translate-y-10 opacity-0"
                                    :style="`transition-delay: ${index * 100}ms;`"
                                    @click="selectedCard = card">
                                    <div class="flex justify-between">
                                        <div x-text="'Reservation ' + card"></div>
                                        <button 
                                            class="text-2xl cursor-pointer bg-transparent border-none" 
                                            @click.stop="cards = cards.filter(c => c !== card); 
                                                        if (selectedCard === card) { 
                                                            selectedCard = cards.length > 0 ? cards[0] : null; 
                                                        } 
                                                        @this.clearCardInputs(card);" 
                                            aria-label="Close Sidebar">
                                            x
                                        </button>
                                    </div>
                                    <div>
                                        <p>Content for card <span x-text="card"></span></p>
                                    </div>
                                    <div x-show="selectedCard === card" class="absolute inset-0 border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Conditional Components -->
                    <form wire:submit.prevent="submitReserve(selectedCard)" @click="open = false" :class="open ? 'blur-[4px]' : ''">
                        <div class="w-[70%] ml-72"> 
                            
                        
                            <!-- card1 -->
                            <div x-show="selectedCard === 1">
                                <div class="bg-green-300 p-4 rounded-lg shadow mt-4  flex flex-col">
                                    <p>Card 1 is selected!</p>
                                </div>

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">1/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">{{ __('Input Vehicle') }}</h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                                    </header>
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                        <div class="slider py-10 overflow-x-auto cursor-grab w-full">
                                            <div class="sliderWrapper overflow-x-scroll flex flex-row space-x-8 h-[18rem]"> 
                                                @foreach ($vehicleTypes as $type)
                                                <button type="button" 
                                                    class="sliderItem rounded-2xl 
                                                            {{ $selectedVehicleTypeId === $type->id ? 'bg-[#87e1ff] text-white' : 'bg-transparent text-gray-800' }} flex-none h-[15rem] w-[15rem] transition-transform duration-300 ease-in-out"
                                                    wire:click="selectVehicleType({{ $type->id }}, 1)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectVehicleType({{ $type->id }}, 1)"
                                                >
                                                    <figure 
                                                        class="relative overflow-hidden bg-white/20 ring-1 ring-black/5 h-[15rem] w-[15rem] rounded-2xl shadow-lg">
                                                        <div class="flex flex-col items-center">
                                                            <img src="{{ asset('storage/' . $type->icon) }}" class="h-full w-full object-cover" alt="VehicleTypeIcon" />
                                                            <div class="absolute bottom-[10%] left-[50%] translate-x-[-50%] z-10">
                                                                <p>{{ $type->name }}</p>
                                                                <p><strong>Price:</strong> P{{ $type->price }}</p>
                                                            </div>
                                                        </div>
                                                    </figure>
                                                    <span wire:loading wire:target="selectVehicleType({{ $type->id }}, 1)">Processing...</span>
                                                </button>
                                            @endforeach
                                            </div>
                                            </div>
                                        <div class="vehicleInput">
                                            <div class="mb-6">
                                                <label for="model" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Model</label>
                                                <input wire:model.debounce.500ms="model" placeholder="Enter Model" type="text" id="model-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled ? 'disabled' : '' }}>
                                            </div>
                                            <div class="mb-6">
                                                <label for="make" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Make</label>
                                                <input wire:model.debounce.500ms="make" placeholder="Enter Make" type="text" id="make-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled ? 'disabled' : '' }}>
                                            </div>
                                            <div class="grid grid-cols-2 gap-10">
                                                <div class="mb-6">
                                                    <label for="color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
                                                    <div class="flex items-center">
                                                        <input wire:model.debounce.500ms="color" type="color" id="color-input" class="cursor-pointer" style="border: none; padding: 0; width: 40px; height: 40px;" {{ !$isVehicleInputEnabled ? 'disabled' : '' }}>
                                                        <input wire:model.debounce.500ms="color" placeholder="Enter Color" type="text" id="color-text-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ml-2" {{ !$isVehicleInputEnabled ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="mb-6">
                                                    <label for="license_plate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">License Plate Number</label>
                                                    <input wire:model.debounce.500ms="license_plate" placeholder="e.g., ABC1234" type="text" id="license_plate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled ? 'disabled' : '' }}>
                                                    <p class="mt-1 text-xs text-gray-500">Please enter the vehicle's license plate number.</p>
                                                    
                                                </div>
                                                </div>
                                                <div>
                                                    <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year</label>
                                                    <div class="relative w-full">
                                                    <div class="absolute inset-y-0 right-[1rem] flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                        </svg>
                                                    </div>
                                                        <div class="mb-6">
                                                            <select wire:model.debounce.500ms="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled ? 'disabled' : '' }}>
                                                                <option value="">Select Year</option>
                                                                @for ($i = date('Y'); $i >= 1970; $i--)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                                <h3 class="text-center">——————————Or—————————</h3>
                                        <div class="mb-6">
                                            <label for="existing_vehicle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Existing Vehicle</label>
                                            <select wire:model.live="selectedVehicleId" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                >
                                                <option value="">Select Vehicle</option>
                                                @foreach ($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </section>



                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">2/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Select Service or Package') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select from the following") }}
                                        </p>
                                    </header>
                                    
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Services</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($service as $serve)
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedServiceId === $serve->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectService({{ $serve->id }}, 1)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectService({{ $serve->id }}, 1)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ asset('storage/' . $serve->icon) }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="ServiceIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $serve->service_name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ $serve->description }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Duration:</strong> {{ $serve->duration }} mins.</p>
                                                    <p><strong>Price:</strong> {{ $serve->price }}</p>
                                                    <p class="ml-auto text-base font-medium text-green-500"><strong>Category:</strong> {{ $serve->category->name }}</p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectService({{ $serve->id }}, 1)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                    <h3 class="text-center">——————————Or—————————</h3>
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Packages</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($package as $pack)
                                        @php
                                            $randomImage = $this->packageImages[array_rand($this->packageImages)];
                                        @endphp
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedPackageId === $pack->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectPackage({{ $pack->id }}, 1)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectPackage({{ $pack->id }}, 1)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ $randomImage }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="PackageIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $pack->name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ Str::limit($pack->description, 5, '...') }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Services:</strong>
                                                        @foreach ($pack->services as $service) 
                                                            <span>{{ $service->service_name }}<br /></span>
                                                        @endforeach
                                                    </p>
                                                    <p class="ml-auto text-base font-medium text-green-500">
                                                        <strong>Discounted Price: {{ $pack->discount }}%</strong> 
                                                        P{{ number_format($pack->original_price - ($pack->original_price * ($pack->discount / 100)), 2) }}
                                                    </p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectPackage({{ $pack->id }}, 1)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                </section>

                               

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">3/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Set Schedule') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select Schedule for reservation") }}
                                        </p>
                                    </header>
                                    
                                    <div class="max-w-md mx-auto p-4">
                                        <!-- Navigation for days -->
                                        <div class="flex justify-between items-center mb-4">
                                            <button class="text-blue-500" wire:click="goToPreviousDays" {{ $currentDay->isSameDay(Carbon::now()) ? 'disabled' : '' }}>
                                                ← Previous
                                            </button>
                                            <div class="text-center font-semibold">{{ $currentDay->format('F Y') }}</div>
                                            <button class="text-blue-500" wire:click="goToNextDays">Next →
                                            </button>
                                        </div>
                                
                                        <!-- Display days and time slots in a 3-column grid layout -->
                                        <div class="grid grid-cols-3 gap-4">
                                            @foreach (array_slice($days, 0, 3) as $day)
                                                <div>
                                                    <!-- Day header -->
                                                    <button type="button" 
                                                    class="text-center cursor-pointer w-full font-semibold p-2 rounded 
                                                        {{ $selectedDate == $day->toDateString() ? 'bg-blue-500 text-white' : '' }}"
                                                    wire:click="selectDay('{{ $day->toDateString() }}', 1)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-gray-400"
                                                    wire:target="selectDay('{{ $day->toDateString() }}', 1)">
                                                    <span wire:loading wire:target="selectDay('{{ $day->toDateString() }}', 1)">Selecting...</span>
                                                    <span wire:loading.remove wire:target="selectDay('{{ $day->toDateString() }}', 1)">{{ $day->format('D, d') }}</span>
                                                </button>
                                                    
                                                    <!-- Time slots for each day -->
                                                    <div class="mt-2">
                                                    @foreach ($availableTimes as $time)
                                                        @php
                                                            $formattedDate = $day->toDateString();
                                                            $formattedTime = Carbon::parse($time)->format('h:i A'); 
                                                            $isDisabled = $this->disabledSchedules->contains(function ($schedule) use ($formattedDate, $formattedTime) {
                                                                return $schedule['date'] === $formattedDate && $schedule['time_slot'] === $formattedTime;
                                                            });
                                                        @endphp
                                                        <button type="button"
                                                        class="w-full p-2 mb-1 rounded-lg 
                                                                {{ $selectedDate == $formattedDate && $selectedTime == $time ? 'bg-blue-500 text-white' : ($selectedDate == $formattedDate ? 'bg-gray-200' : 'bg-gray-100 text-gray-400') }}"
                                                        wire:click="selectTime('{{ $time }}', 1)"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="bg-gray-400"
                                                        wire:target="selectTime('{{ $time }}', 1)"
                                                        {{ $selectedDate != $formattedDate || $isDisabled ? 'disabled' : '' }}>
                                                        
                                                        <!-- Loading indicator for the clicked button only -->
                                                        <span wire:loading wire:target="selectTime('{{ $time }}', 1)">Selecting...</span>
                                                        <span wire:loading.remove wire:target="selectTime('{{ $time }}', 1)">{{ $time }}</span>
                                                    </button>
                                                    @endforeach
                                                </div>
                                
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>


                                <section class="px-4 py-8 h-full">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">4/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Reservation Details') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Review your reservation details") }}
                                        </p>
                                    </header>
                                
                                    <div class="mt-4 flex flex-col space-y-10 h-full justify-center items-center">
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Date: <strong>{{ $selectedDate ?? '' }}</strong></p>
                                        </div>
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Time: <strong>{{ $selectedTime ?? '' }}</strong></p>
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId)
                                                <p>Selected Service: <strong>{{ optional($service->find($selectedServiceId))->service_name ?? 'N/A' }}</strong></p>
                                            @elseif($selectedPackageId)
                                                <p>Selected Package: <strong>{{ optional($package->find($selectedPackageId))->name ?? 'N/A' }}</strong></p>
                                            @else
                                                <p>No service or package selected.</p>
                                            @endif
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId)
                                                <p>Duration: <strong>{{ optional($service->find($selectedServiceId))->duration }} minutes</strong></p>
                                            @elseif($selectedPackageId)
                                                @php
                                                    $selectedPackage = $package->find($selectedPackageId);
                                                    $totalDuration = $selectedPackage ? $selectedPackage->duration : 0;
                                                @endphp
                                                <p>Duration: <strong>{{ $totalDuration }} minutes</strong></p>
                                            @else
                                                <p>Duration: </p>
                                            @endif
                                        </div>
                                
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Total Price: <strong>P{{ $this->totalPrice }}</strong></p>
                                        </div>
                                    </div>
                                </section>
                            </div>









                            <!-- card2 -->
                            <div x-show="selectedCard === 2">
                                <div class="bg-yellow-300 p-4 rounded-lg shadow mt-4  flex flex-col">
                                    <p>Card 2 is selected!</p>
                                </div>

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">1/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">{{ __('Input Vehicle') }}</h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                                    </header>
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                        <div class="slider py-10 overflow-x-auto cursor-grab w-full">
                                            <div class="sliderWrapper overflow-x-scroll flex flex-row space-x-8 h-[18rem]"> 
                                                @foreach ($vehicleTypes as $type)
                                                <button type="button" 
                                                    class="sliderItem rounded-2xl 
                                                            {{ $selectedVehicleTypeId1 === $type->id ? 'bg-[#87e1ff] text-white' : 'bg-transparent text-gray-800' }} flex-none h-[15rem] w-[15rem] transition-transform duration-300 ease-in-out"
                                                    wire:click="selectVehicleType({{ $type->id }}, 2)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectVehicleType({{ $type->id }}, 2)"
                                                >
                                                    <figure 
                                                        class="relative overflow-hidden bg-white/20 ring-1 ring-black/5 h-[15rem] w-[15rem] rounded-2xl shadow-lg">
                                                        <div class="flex flex-col items-center">
                                                            <img src="{{ asset('storage/' . $type->icon) }}" class="h-full w-full object-cover" alt="VehicleTypeIcon" />
                                                            <div class="absolute bottom-[10%] left-[50%] translate-x-[-50%] z-10">
                                                                <p>{{ $type->name }}</p>
                                                                <p><strong>Price:</strong> P{{ $type->price }}</p>
                                                            </div>
                                                        </div>
                                                    </figure>
                                                    <span wire:loading wire:target="selectVehicleType({{ $type->id }}, 2)">Processing...</span>
                                                </button>
                                            @endforeach
                                            </div>
                                            </div>
                                        <div class="vehicleInput">
                                            <div class="mb-6">
                                                <label for="model1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Model</label>
                                                <input wire:model.debounce.500ms="model1" placeholder="Enter Model" type="text" id="model1-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled1 ? 'disabled' : '' }}>
                                            </div>
                                            <div class="mb-6">
                                                <label for="make1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Make</label>
                                                <input wire:model.debounce.500ms="make1" placeholder="Enter Make" type="text" id="make1-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled1 ? 'disabled' : '' }}>
                                            </div>
                                            <div class="grid grid-cols-2 gap-10">
                                                <div class="mb-6">
                                                    <label for="color1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
                                                    <div class="flex items-center">
                                                        <input wire:model.debounce.500ms="color1" type="color" id="color1-input" class="cursor-pointer" style="border: none; padding: 0; width: 40px; height: 40px;" {{ !$isVehicleInputEnabled1 ? 'disabled' : '' }}>
                                                        <input wire:model.debounce.500ms="color1" placeholder="Enter Color" type="text" id="color-text1-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ml-2" {{ !$isVehicleInputEnabled1 ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="mb-6">
                                                    <label for="license_plate1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">License Plate Number</label>
                                                    <input wire:model.debounce.500ms="license_plate1" placeholder="e.g., ABC1234" type="text" id="license_plate1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled1 ? 'disabled' : '' }}>
                                                    <p class="mt-1 text-xs text-gray-500">Please enter the vehicle's license plate number.</p>
                                                    
                                                </div>
                                                </div>
                                                <div>
                                                    <label for="year1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year</label>
                                                    <div class="relative w-full">
                                                    <div class="absolute inset-y-0 right-[1rem] flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                        </svg>
                                                    </div>
                                                        <div class="mb-6">
                                                            <select wire:model.debounce.500ms="year1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled1 ? 'disabled' : '' }}>
                                                                <option value="">Select Year</option>
                                                                @for ($i = date('Y'); $i >= 1970; $i--)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                                <h3 class="text-center">——————————Or—————————</h3>
                                        <div class="mb-6">
                                            <label for="existing_vehicle1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Existing Vehicle</label>
                                            <select wire:model.live="selectedVehicleId1" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                >
                                                <option value="">Select Vehicle</option>
                                                @foreach ($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </section>



                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">2/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Select Service or Package') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select from the following") }}
                                        </p>
                                    </header>
                                    
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Services</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($service1 as $serve)
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedServiceId1 === $serve->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectService({{ $serve->id }}, 2)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectService({{ $serve->id }}, 2)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ asset('storage/' . $serve->icon) }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="ServiceIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $serve->service_name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ $serve->description }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Duration:</strong> {{ $serve->duration }} mins.</p>
                                                    <p><strong>Price:</strong> {{ $serve->price }}</p>
                                                    <p class="ml-auto text-base font-medium text-green-500"><strong>Category:</strong> {{ $serve->category->name }}</p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectService({{ $serve->id }}, 2)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                    <h3 class="text-center">——————————Or—————————</h3>
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Packages</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($package as $pack)
                                        @php
                                            $randomImage = $this->packageImages[array_rand($this->packageImages)];
                                        @endphp
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedPackageId1 === $pack->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectPackage({{ $pack->id }}, 2)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectPackage({{ $pack->id }}, 2)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ $randomImage }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="PackageIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $pack->name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ Str::limit($pack->description, 5, '...') }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Services:</strong>
                                                        @foreach ($pack->services as $service) 
                                                            <span>{{ $service->service_name }}<br /></span>
                                                        @endforeach
                                                    </p>
                                                    <p class="ml-auto text-base font-medium text-green-500">
                                                        <strong>Discounted Price: {{ $pack->discount }}%</strong> 
                                                        P{{ number_format($pack->original_price - ($pack->original_price * ($pack->discount / 100)), 2) }}
                                                    </p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectPackage({{ $pack->id }}, 2)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                </section>

                               

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">3/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Set Schedule') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select Schedule for reservation") }}
                                        </p>
                                    </header>
                                    
                                    <div class="max-w-md mx-auto p-4">
                                        <!-- Navigation for days -->
                                        <div class="flex justify-between items-center mb-4">
                                            <button class="text-blue-500" wire:click="goToPreviousDays" {{ $currentDay->isSameDay(Carbon::now()) ? 'disabled' : '' }}>
                                                ← Previous
                                            </button>
                                            <div class="text-center font-semibold">{{ $currentDay->format('F Y') }}</div>
                                            <button class="text-blue-500" wire:click="goToNextDays">Next →
                                            </button>
                                        </div>
                                
                                        <!-- Display days and time slots in a 3-column grid layout -->
                                        <div class="grid grid-cols-3 gap-4">
                                            @foreach (array_slice($days, 0, 3) as $day)
                                                <div>
                                                    <!-- Day header -->
                                                    <button type="button" 
                                                    class="text-center cursor-pointer w-full font-semibold p-2 rounded 
                                                        {{ $selectedDate1 == $day->toDateString() ? 'bg-blue-500 text-white' : '' }}"
                                                    wire:click="selectDay('{{ $day->toDateString() }}', 2)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-gray-400"
                                                    wire:target="selectDay('{{ $day->toDateString() }}', 2)">
                                                    <span wire:loading wire:target="selectDay('{{ $day->toDateString() }}', 2)">Selecting...</span>
                                                    <span wire:loading.remove wire:target="selectDay('{{ $day->toDateString() }}', 2)">{{ $day->format('D, d') }}</span>
                                                </button>
                                                    
                                                    <!-- Time slots for each day -->
                                                    <div class="mt-2">
                                                    @foreach ($availableTimes as $time)
                                                        @php
                                                            $formattedDate = $day->toDateString();
                                                            $formattedTime = Carbon::parse($time)->format('h:i A'); 
                                                            $isDisabled = $this->disabledSchedules->contains(function ($schedule) use ($formattedDate, $formattedTime) {
                                                                return $schedule['date'] === $formattedDate && $schedule['time_slot'] === $formattedTime;
                                                            });
                                                        @endphp
                                                        <button type="button"
                                                        class="w-full p-2 mb-1 rounded-lg 
                                                                {{ $selectedDate1 == $formattedDate && $selectedTime1 == $time ? 'bg-blue-500 text-white' : ($selectedDate1 == $formattedDate ? 'bg-gray-200' : 'bg-gray-100 text-gray-400') }}"
                                                        wire:click="selectTime('{{ $time }}', 2)"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="bg-gray-400"
                                                        wire:target="selectTime('{{ $time }}', 2)"
                                                        {{ $selectedDate1 != $formattedDate || $isDisabled ? 'disabled' : '' }}>
                                                        
                                                        <!-- Loading indicator for the clicked button only -->
                                                        <span wire:loading wire:target="selectTime('{{ $time }}', 2)">Selecting...</span>
                                                        <span wire:loading.remove wire:target="selectTime('{{ $time }}', 2)">{{ $time }}</span>
                                                    </button>
                                                    @endforeach
                                                </div>
                                
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>


                                <section class="px-4 py-8 h-full">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">4/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Reservation Details') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Review your reservation details") }}
                                        </p>
                                    </header>
                                
                                    <div class="mt-4 flex flex-col space-y-10 h-full justify-center items-center">
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Date: <strong>{{ $selectedDate1 ?? '' }}</strong></p>
                                        </div>
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Time: <strong>{{ $selectedTime1 ?? '' }}</strong></p>
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId1)
                                                <p>Selected Service: <strong>{{ optional($service->find($selectedServiceId1))->service_name ?? 'N/A' }}</strong></p>
                                            @elseif($selectedPackageId1)
                                                <p>Selected Package: <strong>{{ optional($package->find($selectedPackageId1))->name ?? 'N/A' }}</strong></p>
                                            @else
                                                <p>No service or package selected.</p>
                                            @endif
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId1)
                                                <p>Duration: <strong>{{ optional($service->find($selectedServiceId1))->duration }} minutes</strong></p>
                                            @elseif($selectedPackageId1)
                                                @php
                                                    $selectedPackage = $package->find($selectedPackageId1);
                                                    $totalDuration = $selectedPackage ? $selectedPackage->duration : 0;
                                                @endphp
                                                <p>Duration: <strong>{{ $totalDuration }} minutes</strong></p>
                                            @else
                                                <p>Duration: </p>
                                            @endif
                                        </div>
                                
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Total Price: <strong>P{{ $this->totalPrice1 }}</strong></p>
                                        </div>
                                    </div>
                                </section>
                            </div>







                            <!-- card3 -->
                            <div x-show="selectedCard === 3">
                                <div class="bg-red-300 p-4 rounded-lg shadow mt-4  flex flex-col">
                                    <p>Card 3 is selected!</p>
                                </div>

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">1/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">{{ __('Input Vehicle') }}</h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                                    </header>
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                        <div class="slider py-10 overflow-x-auto cursor-grab w-full">
                                            <div class="sliderWrapper overflow-x-scroll flex flex-row space-x-8 h-[18rem]"> 
                                                @foreach ($vehicleTypes as $type)
                                                <button type="button" 
                                                    class="sliderItem rounded-2xl 
                                                            {{ $selectedVehicleTypeId2 === $type->id ? 'bg-[#87e1ff] text-white' : 'bg-transparent text-gray-800' }} flex-none h-[15rem] w-[15rem] transition-transform duration-300 ease-in-out"
                                                    wire:click="selectVehicleType({{ $type->id }}, 3)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectVehicleType({{ $type->id }}, 3)"
                                                >
                                                    <figure 
                                                        class="relative overflow-hidden bg-white/20 ring-1 ring-black/5 h-[15rem] w-[15rem] rounded-2xl shadow-lg">
                                                        <div class="flex flex-col items-center">
                                                            <img src="{{ asset('storage/' . $type->icon) }}" class="h-full w-full object-cover" alt="VehicleTypeIcon" />
                                                            <div class="absolute bottom-[10%] left-[50%] translate-x-[-50%] z-10">
                                                                <p>{{ $type->name }}</p>
                                                                <p><strong>Price:</strong> P{{ $type->price }}</p>
                                                            </div>
                                                        </div>
                                                    </figure>
                                                    <span wire:loading wire:target="selectVehicleType({{ $type->id }}, 3)">Processing...</span>
                                                </button>
                                            @endforeach
                                            </div>
                                            </div>
                                        <div class="vehicleInput">
                                            <div class="mb-6">
                                                <label for="model2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Model</label>
                                                <input wire:model.debounce.500ms="model2" placeholder="Enter Model" type="text" id="model2-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled2 ? 'disabled' : '' }}>
                                            </div>
                                            <div class="mb-6">
                                                <label for="make2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Make</label>
                                                <input wire:model.debounce.500ms="make2" placeholder="Enter Make" type="text" id="make2-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled2 ? 'disabled' : '' }}>
                                            </div>
                                            <div class="grid grid-cols-2 gap-10">
                                                <div class="mb-6">
                                                    <label for="color2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
                                                    <div class="flex items-center">
                                                        <input wire:model.debounce.500ms="color2" type="color" id="color2-input" class="cursor-pointer" style="border: none; padding: 0; width: 40px; height: 40px;" {{ !$isVehicleInputEnabled2 ? 'disabled' : '' }}>
                                                        <input wire:model.debounce.500ms="color2" placeholder="Enter Color" type="text" id="color-text2-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ml-2" {{ !$isVehicleInputEnabled2 ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="mb-6">
                                                    <label for="license_plate2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">License Plate Number</label>
                                                    <input wire:model.debounce.500ms="license_plate2" placeholder="e.g., ABC1234" type="text" id="license_plate2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled2 ? 'disabled' : '' }}>
                                                    <p class="mt-1 text-xs text-gray-500">Please enter the vehicle's license plate number.</p>
                                                    
                                                </div>
                                                </div>
                                                <div>
                                                    <label for="year2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year</label>
                                                    <div class="relative w-full">
                                                    <div class="absolute inset-y-0 right-[1rem] flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                        </svg>
                                                    </div>
                                                        <div class="mb-6">
                                                            <select wire:model.debounce.500ms="year2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled2 ? 'disabled' : '' }}>
                                                                <option value="">Select Year</option>
                                                                @for ($i = date('Y'); $i >= 1970; $i--)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                                <h3 class="text-center">——————————Or—————————</h3>
                                        <div class="mb-6">
                                            <label for="existing_vehicle2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Existing Vehicle</label>
                                            <select wire:model.live="selectedVehicleId2" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                >
                                                <option value="">Select Vehicle</option>
                                                @foreach ($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </section>



                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">2/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Select Service or Package') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select from the following") }}
                                        </p>
                                    </header>
                                    
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Services</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($service2 as $serve)
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedServiceId2 === $serve->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectService({{ $serve->id }}, 3)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectService({{ $serve->id }}, 3)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ asset('storage/' . $serve->icon) }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="ServiceIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $serve->service_name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ $serve->description }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Duration:</strong> {{ $serve->duration }} mins.</p>
                                                    <p><strong>Price:</strong> {{ $serve->price }}</p>
                                                    <p class="ml-auto text-base font-medium text-green-500"><strong>Category:</strong> {{ $serve->category->name }}</p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectService({{ $serve->id }}, 3)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                    <h3 class="text-center">——————————Or—————————</h3>
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Packages</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($package as $pack)
                                        @php
                                            $randomImage = $this->packageImages[array_rand($this->packageImages)];
                                        @endphp
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedPackageId2 === $pack->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectPackage({{ $pack->id }}, 3)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectPackage({{ $pack->id }}, 3)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ $randomImage }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="PackageIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $pack->name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ Str::limit($pack->description, 5, '...') }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Services:</strong>
                                                        @foreach ($pack->services as $service) 
                                                            <span>{{ $service->service_name }}<br /></span>
                                                        @endforeach
                                                    </p>
                                                    <p class="ml-auto text-base font-medium text-green-500">
                                                        <strong>Discounted Price: {{ $pack->discount }}%</strong> 
                                                        P{{ number_format($pack->original_price - ($pack->original_price * ($pack->discount / 100)), 2) }}
                                                    </p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectPackage({{ $pack->id }}, 3)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                </section>

                               

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">3/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Set Schedule') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select Schedule for reservation") }}
                                        </p>
                                    </header>
                                    
                                    <div class="max-w-md mx-auto p-4">
                                        <!-- Navigation for days -->
                                        <div class="flex justify-between items-center mb-4">
                                            <button class="text-blue-500" wire:click="goToPreviousDays" {{ $currentDay->isSameDay(Carbon::now()) ? 'disabled' : '' }}>
                                                ← Previous
                                            </button>
                                            <div class="text-center font-semibold">{{ $currentDay->format('F Y') }}</div>
                                            <button class="text-blue-500" wire:click="goToNextDays">Next →
                                            </button>
                                        </div>
                                
                                        <!-- Display days and time slots in a 3-column grid layout -->
                                        <div class="grid grid-cols-3 gap-4">
                                            @foreach (array_slice($days, 0, 3) as $day)
                                                <div>
                                                    <!-- Day header -->
                                                    <button type="button" 
                                                    class="text-center cursor-pointer w-full font-semibold p-2 rounded 
                                                        {{ $selectedDate2 == $day->toDateString() ? 'bg-blue-500 text-white' : '' }}"
                                                    wire:click="selectDay('{{ $day->toDateString() }}', 3)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-gray-400"
                                                    wire:target="selectDay('{{ $day->toDateString() }}', 3)">
                                                    <span wire:loading wire:target="selectDay('{{ $day->toDateString() }}', 3)">Selecting...</span>
                                                    <span wire:loading.remove wire:target="selectDay('{{ $day->toDateString() }}',3)">{{ $day->format('D, d') }}</span>
                                                </button>
                                                    
                                                    <!-- Time slots for each day -->
                                                    <div class="mt-2">
                                                    @foreach ($availableTimes as $time)
                                                        @php
                                                            $formattedDate = $day->toDateString();
                                                            $formattedTime = Carbon::parse($time)->format('h:i A'); 
                                                            $isDisabled = $this->disabledSchedules->contains(function ($schedule) use ($formattedDate, $formattedTime) {
                                                                return $schedule['date'] === $formattedDate && $schedule['time_slot'] === $formattedTime;
                                                            });
                                                        @endphp
                                                        <button type="button"
                                                        class="w-full p-2 mb-1 rounded-lg 
                                                                {{ $selectedDate2 == $formattedDate && $selectedTime2 == $time ? 'bg-blue-500 text-white' : ($selectedDate2 == $formattedDate ? 'bg-gray-200' : 'bg-gray-100 text-gray-400') }}"
                                                        wire:click="selectTime('{{ $time }}', 3)"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="bg-gray-400"
                                                        wire:target="selectTime('{{ $time }}', 3)"
                                                        {{ $selectedDate2 != $formattedDate || $isDisabled ? 'disabled' : '' }}>
                                                        
                                                        <!-- Loading indicator for the clicked button only -->
                                                        <span wire:loading wire:target="selectTime('{{ $time }}', 3)">Selecting...</span>
                                                        <span wire:loading.remove wire:target="selectTime('{{ $time }}', 3)">{{ $time }}</span>
                                                    </button>
                                                    @endforeach
                                                </div>
                                
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>


                                <section class="px-4 py-8 h-full">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">4/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Reservation Details') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Review your reservation details") }}
                                        </p>
                                    </header>
                                
                                    <div class="mt-4 flex flex-col space-y-10 h-full justify-center items-center">
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Date: <strong>{{ $selectedDate2 ?? '' }}</strong></p>
                                        </div>
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Time: <strong>{{ $selectedTime2 ?? '' }}</strong></p>
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId2)
                                                <p>Selected Service: <strong>{{ optional($service->find($selectedServiceId2))->service_name ?? 'N/A' }}</strong></p>
                                            @elseif($selectedPackageId2)
                                                <p>Selected Package: <strong>{{ optional($package->find($selectedPackageId2))->name ?? 'N/A' }}</strong></p>
                                            @else
                                                <p>No service or package selected.</p>
                                            @endif
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId2)
                                                <p>Duration: <strong>{{ optional($service->find($selectedServiceId2))->duration }} minutes</strong></p>
                                            @elseif($selectedPackageId2)
                                                @php
                                                    $selectedPackage = $package->find($selectedPackageId2);
                                                    $totalDuration = $selectedPackage ? $selectedPackage->duration : 0;
                                                @endphp
                                                <p>Duration: <strong>{{ $totalDuration }} minutes</strong></p>
                                            @else
                                                <p>Duration: </p>
                                            @endif
                                        </div>
                                
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Total Price: <strong>P{{ $this->totalPrice2 }}</strong></p>
                                        </div>
                                    </div>
                                </section>
                            </div>








                            <!-- card4 -->
                            <div x-show="selectedCard === 4">
                                <div class="bg-blue-300 p-4 rounded-lg shadow mt-4  flex flex-col">
                                    <p>Card 4 is selected!</p>
                                </div>

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">1/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">{{ __('Input Vehicle') }}</h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                                    </header>
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Available Vehicle Types</h3>
                                        <div class="slider py-10 overflow-x-auto cursor-grab w-full">
                                            <div class="sliderWrapper overflow-x-scroll flex flex-row space-x-8 h-[18rem]"> 
                                                @foreach ($vehicleTypes as $type)
                                                <button type="button" 
                                                    class="sliderItem rounded-2xl 
                                                            {{ $selectedVehicleTypeId3 === $type->id ? 'bg-[#87e1ff] text-white' : 'bg-transparent text-gray-800' }} flex-none h-[15rem] w-[15rem] transition-transform duration-300 ease-in-out"
                                                    wire:click="selectVehicleType({{ $type->id }}, 4)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectVehicleType({{ $type->id }}, 4)"
                                                >
                                                    <figure 
                                                        class="relative overflow-hidden bg-white/20 ring-1 ring-black/5 h-[15rem] w-[15rem] rounded-2xl shadow-lg">
                                                        <div class="flex flex-col items-center">
                                                            <img src="{{ asset('storage/' . $type->icon) }}" class="h-full w-full object-cover" alt="VehicleTypeIcon" />
                                                            <div class="absolute bottom-[10%] left-[50%] translate-x-[-50%] z-10">
                                                                <p>{{ $type->name }}</p>
                                                                <p><strong>Price:</strong> P{{ $type->price }}</p>
                                                            </div>
                                                        </div>
                                                    </figure>
                                                    <span wire:loading wire:target="selectVehicleType({{ $type->id }}, 4)">Processing...</span>
                                                </button>
                                            @endforeach
                                            </div>
                                            </div>
                                        <div class="vehicleInput">
                                            <div class="mb-6">
                                                <label for="model3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Model</label>
                                                <input wire:model.debounce.500ms="model3" placeholder="Enter Model" type="text" id="model3-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled3 ? 'disabled' : '' }}>
                                            </div>
                                            <div class="mb-6">
                                                <label for="make3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Make</label>
                                                <input wire:model.debounce.500ms="make3" placeholder="Enter Make" type="text" id="make3-input" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ !$isVehicleInputEnabled3 ? 'disabled' : '' }}>
                                            </div>
                                            <div class="grid grid-cols-2 gap-10">
                                                <div class="mb-6">
                                                    <label for="color3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
                                                    <div class="flex items-center">
                                                        <input wire:model.debounce.500ms="color3" type="color" id="color3-input" class="cursor-pointer" style="border: none; padding: 0; width: 40px; height: 40px;" {{ !$isVehicleInputEnabled3 ? 'disabled' : '' }}>
                                                        <input wire:model.debounce.500ms="color3" placeholder="Enter Color" type="text" id="color-text3-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ml-2" {{ !$isVehicleInputEnabled3 ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="mb-6">
                                                    <label for="license_plate3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">License Plate Number</label>
                                                    <input wire:model.debounce.500ms="license_plate3" placeholder="e.g., ABC1234" type="text" id="license_plate3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled3 ? 'disabled' : '' }}>
                                                    <p class="mt-1 text-xs text-gray-500">Please enter the vehicle's license plate number.</p>
                                                    
                                                </div>
                                                </div>
                                                <div>
                                                    <label for="year3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year</label>
                                                    <div class="relative w-full">
                                                    <div class="absolute inset-y-0 right-[1rem] flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                        </svg>
                                                    </div>
                                                        <div class="mb-6">
                                                            <select wire:model.debounce.500ms="year3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" {{ !$isVehicleInputEnabled3 ? 'disabled' : '' }}>
                                                                <option value="">Select Year</option>
                                                                @for ($i = date('Y'); $i >= 1970; $i--)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                                <h3 class="text-center">——————————Or—————————</h3>
                                        <div class="mb-6">
                                            <label for="existing_vehicle3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Existing Vehicle</label>
                                            <select wire:model.live="selectedVehicleId3" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                >
                                                <option value="">Select Vehicle</option>
                                                @foreach ($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </section>



                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">2/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Select Service or Package') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select from the following") }}
                                        </p>
                                    </header>
                                    
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Services</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($service2 as $serve)
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedServiceId3 === $serve->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectService({{ $serve->id }}, 4)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectService({{ $serve->id }}, 4)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ asset('storage/' . $serve->icon) }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="ServiceIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $serve->service_name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ $serve->description }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Duration:</strong> {{ $serve->duration }} mins.</p>
                                                    <p><strong>Price:</strong> {{ $serve->price }}</p>
                                                    <p class="ml-auto text-base font-medium text-green-500"><strong>Category:</strong> {{ $serve->category->name }}</p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectService({{ $serve->id }}, 4)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                    <h3 class="text-center">——————————Or—————————</h3>
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Available Packages</h3>
                                    <div class="grid grid-cols-3 gap-6"> 
                                        @foreach ($package as $pack)
                                        @php
                                            $randomImage = $this->packageImages[array_rand($this->packageImages)];
                                        @endphp
                                            <button class="itemService flex justify-center items-center flex-col w-full transform overflow-hidden rounded-lg dark:bg-slate-800 shadow-md duration-300 hover:scale-105 hover:shadow-lg {{ $selectedPackageId3 === $pack->id ? 'bg-blue-300 text-white' : 'bg-white text-gray-900' }}"
                                                wire:click="selectPackage({{ $pack->id }}, 4)" type="button" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-blue-500"
                                                    wire:target="selectPackage({{ $pack->id }}, 4)"
                                                >
                                            <figure class='h-[400px] w-[90%] flex flex-col justify-center items-center'>
                                                <img src="{{ $randomImage }}" class="h-[40%] w-[100%] object-cover object-center flex self-center" alt="PackageIcon" />
                                                <div class="p-4 w-full">
                                                    <p class="mb-2 text-lg font-medium dark:text-white text-gray-900"><strong>Name:</strong> {{ $pack->name }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Description:</strong> {{ Str::limit($pack->description, 5, '...') }}</p>
                                                    <p class="mb-2 text-base dark:text-gray-300 text-gray-700"><strong>Services:</strong>
                                                        @foreach ($pack->services as $service) 
                                                            <span>{{ $service->service_name }}<br /></span>
                                                        @endforeach
                                                    </p>
                                                    <p class="ml-auto text-base font-medium text-green-500">
                                                        <strong>Discounted Price: {{ $pack->discount }}%</strong> 
                                                        P{{ number_format($pack->original_price - ($pack->original_price * ($pack->discount / 100)), 2) }}
                                                    </p>
                                                </div>
                                            </figure>
                                            <span wire:loading wire:target="selectPackage({{ $pack->id }}, 4)">Processing...</span>
                                        </button>
                                        @endforeach
                                    </div>
                                </section>

                               

                                <section class="px-4 py-8">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">3/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Set Schedule') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Select Schedule for reservation") }}
                                        </p>
                                    </header>
                                    
                                    <div class="max-w-md mx-auto p-4">
                                        <!-- Navigation for days -->
                                        <div class="flex justify-between items-center mb-4">
                                            <button class="text-blue-500" wire:click="goToPreviousDays" {{ $currentDay->isSameDay(Carbon::now()) ? 'disabled' : '' }}>
                                                ← Previous
                                            </button>
                                            <div class="text-center font-semibold">{{ $currentDay->format('F Y') }}</div>
                                            <button class="text-blue-500" wire:click="goToNextDays">Next →
                                            </button>
                                        </div>
                                
                                        <!-- Display days and time slots in a 3-column grid layout -->
                                        <div class="grid grid-cols-3 gap-4">
                                            @foreach (array_slice($days, 0, 3) as $day)
                                                <div>
                                                    <!-- Day header -->
                                                    <button type="button" 
                                                    class="text-center cursor-pointer w-full font-semibold p-2 rounded 
                                                        {{ $selectedDate3 == $day->toDateString() ? 'bg-blue-500 text-white' : '' }}"
                                                    wire:click="selectDay('{{ $day->toDateString() }}', 4)" 
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="bg-gray-400"
                                                    wire:target="selectDay('{{ $day->toDateString() }}', 4)">
                                                    <span wire:loading wire:target="selectDay('{{ $day->toDateString() }}', 4)">Selecting...</span>
                                                    <span wire:loading.remove wire:target="selectDay('{{ $day->toDateString() }}', 4)">{{ $day->format('D, d') }}</span>
                                                </button>
                                                    
                                                    <!-- Time slots for each day -->
                                                    <div class="mt-2">
                                                    @foreach ($availableTimes as $time)
                                                        @php
                                                            $formattedDate = $day->toDateString();
                                                            $formattedTime = Carbon::parse($time)->format('h:i A'); 
                                                            $isDisabled = $this->disabledSchedules->contains(function ($schedule) use ($formattedDate, $formattedTime) {
                                                                return $schedule['date'] === $formattedDate && $schedule['time_slot'] === $formattedTime;
                                                            });
                                                        @endphp
                                                        <button type="button"
                                                        class="w-full p-2 mb-1 rounded-lg 
                                                                {{ $selectedDate3 == $formattedDate && $selectedTime3 == $time ? 'bg-blue-500 text-white' : ($selectedDate3 == $formattedDate ? 'bg-gray-200' : 'bg-gray-100 text-gray-400') }}"
                                                        wire:click="selectTime('{{ $time }}', 4)"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="bg-gray-400"
                                                        wire:target="selectTime('{{ $time }}', 4)"
                                                        {{ $selectedDate3 != $formattedDate || $isDisabled ? 'disabled' : '' }}>
                                                        
                                                        <!-- Loading indicator for the clicked button only -->
                                                        <span wire:loading wire:target="selectTime('{{ $time }}', 4)">Selecting...</span>
                                                        <span wire:loading.remove wire:target="selectTime('{{ $time }}', 4)">{{ $time }}</span>
                                                    </button>
                                                    @endforeach
                                                </div>
                                
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>


                                <section class="px-4 py-8 h-full">
                                    <header>
                                        <div class="flex flow-row gap-2 items-center">
                                            <div class="bg-[#6c8ee5] h-[60px] w-[60px] rounded-full grid place-items-center">
                                                <span class="text-white">4/4</span>
                                            </div>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Reservation Details') }}
                                            </h2>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __("Review your reservation details") }}
                                        </p>
                                    </header>
                                
                                    <div class="mt-4 flex flex-col space-y-10 h-full justify-center items-center">
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Date: <strong>{{ $selectedDate3 ?? '' }}</strong></p>
                                        </div>
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Selected Time: <strong>{{ $selectedTime3 ?? '' }}</strong></p>
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId3)
                                                <p>Selected Service: <strong>{{ optional($service->find($selectedServiceId3))->service_name ?? 'N/A' }}</strong></p>
                                            @elseif($selectedPackageId3)
                                                <p>Selected Package: <strong>{{ optional($package->find($selectedPackageId3))->name ?? 'N/A' }}</strong></p>
                                            @else
                                                <p>No service or package selected.</p>
                                            @endif
                                        </div>
                                        
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            @if($selectedServiceId3)
                                                <p>Duration: <strong>{{ optional($service->find($selectedServiceId3))->duration }} minutes</strong></p>
                                            @elseif($selectedPackageId3)
                                                @php
                                                    $selectedPackage = $package->find($selectedPackageId3);
                                                    $totalDuration = $selectedPackage ? $selectedPackage->duration : 0;
                                                @endphp
                                                <p>Duration: <strong>{{ $totalDuration }} minutes</strong></p>
                                            @else
                                                <p>Duration: </p>
                                            @endif
                                        </div>
                                
                                        <div class="border-[#6c8ee5] border-2 h-[5rem] flex items-center pl-9 rounded-2xl w-full">
                                            <p>Total Price: <strong>P{{ $this->totalPrice3 }}</strong></p>
                                        </div>
                                    </div>
                                </section>
                            </div>



                        <div x-show="!selectedCard" class="bg-red-300 p-4 rounded-lg shadow mt-4">
                            <p>No card is selected. Please select a card to proceed with your reservation.</p>
                        </div>

                    

                           


                            
                            
                            
                            
                            
                            <div class="mt-4 w-full flex flex-col items-center justify-center">
                                <p class="text-center mt-1 text-xs text-red-600">{{ $licensePlateError}}</p>
                                <label class="w-full text-center">
                                    <input type="checkbox" wire:model="isAgreed" class="mr-2">
                                    I agree to the 
                                    <span wire:click="$dispatch('openModal', { component: 'termsagreement' })" class="text-blue-400 font-bold">
                                        Terms and Conditions
                                    </span>.
                                </label>
                            </div>
                        </div>
                        <div class="ml-72 w-[70%] flex justify-center items-center">
                            <button class="bg-blue-400 text-white rounded-[5px] w-[80%]" :disabled="!$wire.isSubmitEnabled || !$wire.isAgreed"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="bg-blue-500"
                                    wire:target="submitReserve"
                                    >
                                    <span wire:loading wire:target="submitReserve">Processing...</span>
                                    <span wire:loading.remove wire:target="submitReserve">{{ __('Reserve') }}</span>
                            </button>
                        </div>
                    </form>
                </div>


                @if (session()->has('message'))
                    <div class="mt-4 text-green-600">
                        {{ session('message') }}
                    </div>
                @endif
                    <style>
                      [disabled] {
                          opacity: 0.5;
                          cursor: not-allowed;
                      }
                      .sliderItem {
                          transition: transform 0.3s ease-in-out, filter 0.3s ease-in-out;
                      }
                      .sliderItem.blurred {
                          filter: blur(2px); 
                      }

                      .translate-x-full {
                            transform: translateX(100%);
                        }
                  </style>
              
                  <script>
                      document.addEventListener('DOMContentLoaded', function() {
                         const sliderWrapper = document.querySelector('.sliderWrapper');
                         const sliderItems = document.querySelectorAll('.sliderItem');
                         
                          let isSliding = false;
                          
                          
                          
                          sliderWrapper.addEventListener('mousedown', function() {
                              isSliding = true;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(0.8)'; 
                                  item.classList.add('blurred'); 
                              });
                          });
              
                          sliderWrapper.addEventListener('touchstart', function() {
                              isSliding = true;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(0.8)'; 
                                  item.classList.add('blurred'); 
                              });
                          });
                          
                          sliderWrapper.addEventListener('mouseup', function() {
                              isSliding = false;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(1)'; 
                                  item.classList.remove('blurred'); 
                              });
                          });
                          
                          sliderWrapper.addEventListener('touchend', function() {
                              isSliding = false;
                              sliderItems.forEach(item => {
                                  item.style.transform = 'scale(1)'; 
                                  item.classList.remove('blurred'); 
                              });
                          });
                          sliderWrapper.addEventListener('mousemove', function() {
                              if (isSliding) {
                                  sliderItems.forEach(item => {
                                      item.style.transform = 'scale(0.8)';
                                      item.classList.add('blurred'); 
                                  });
                              }
                          });
              
                          sliderWrapper.addEventListener('touchmove', function() {
                              if (isSliding) {
                                  sliderItems.forEach(item => {
                                      item.style.transform = 'scale(0.8)';
                                      item.classList.add('blurred'); 
                                  });
                              }
                          });
              
                          
                          sliderItems.forEach(item => {
                              item.addEventListener('click', function() {
                                  figure.forEach(i => {
                                      i.classList.remove('selected');
                                      i.style.transform = 'scale(1)'; 
                                  });
                                  this.classList.add('selected');
                              });
                          });





                      });



                      
                  </script>
                  
              

            </div>
      