<div>
    <section class="w-full">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('ID Verification')" :subheading="__('Verify your identity to unlock all features')">
            <div class="my-6 w-full space-y-6">

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($currentVerification && $currentVerification->isValid())
        <!-- Verified Status -->
        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center space-x-3 mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-green-900">ID Verified</h3>
                    <p class="text-sm text-green-700">Your identity has been verified</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Verified on:</span>
                    <span class="font-medium text-gray-900">{{ $currentVerification->verified_at->format('M d, Y') }}</span>
                </div>
                @if($currentVerification->expires_at)
                <div>
                    <span class="text-gray-600">Expires on:</span>
                    <span class="font-medium text-gray-900">{{ $currentVerification->expires_at->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    @elseif($currentVerification && $currentVerification->isPending())
        <!-- Pending Status -->
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 mb-6">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-900">Verification Pending</h3>
                    <p class="text-sm text-yellow-700">We are reviewing your ID documents. This typically takes 1-2 business days.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Submit ID for Verification</h3>
                <p class="text-sm text-gray-600">Upload a government-issued ID to verify your identity</p>
            </div>

            <form method="POST" action="{{ route('settings.verification.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- ID Type and Expiry Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Type *</label>
                        <select name="id_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                            <option value="">Select ID type...</option>
                            <option value="passport" @selected(old('id_type') === 'passport')>Passport</option>
                            <option value="national_card" @selected(old('id_type') === 'national_card')>Ghana Card</option>
                            <option value="drivers_license" @selected(old('id_type') === 'drivers_license')>Driver's License</option>
                        </select>
                        @error('id_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Expiry Date *</label>
                        <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600" />
                        @error('expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Names -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="As shown on ID" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600" />
                        @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="As shown on ID" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600" />
                        @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other Names</label>
                        <input type="text" name="other_names" value="{{ old('other_names') }}" placeholder="Middle name(s) if any" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Number</label>
                        <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="Optional" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600" />
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div x-data="{ preview: null }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Front of ID *</label>
                        <input
                            type="file"
                            name="front_image"
                            accept="image/jpeg,image/png,image/jpg,application/pdf"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer"
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                        />
                        <template x-if="preview">
                            <img :src="preview" class="mt-2 w-full h-48 object-cover rounded-lg border border-gray-300">
                        </template>
                        @error('front_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ preview: null }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Back of ID</label>
                        <input
                            type="file"
                            name="back_image"
                            accept="image/jpeg,image/png,image/jpg,application/pdf"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer"
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                        />
                        <template x-if="preview">
                            <img :src="preview" class="mt-2 w-full h-48 object-cover rounded-lg border border-gray-300">
                        </template>
                        @error('back_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">Required for Ghana Card and Driver's License</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">
                        {{ __('Submit for Verification') }}
                    </flux:button>
                </div>
            </form>
        </div>
    @endif

    <!-- Verification History -->
    @if($verifications->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification History</h3>
            <div class="space-y-4">
                @foreach($verifications as $verification)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">{{ $verification->getIdTypeLabel() }}</span>
                                @if($verification->isValid())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Valid
                                    </span>
                                @elseif($verification->isPending())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($verification->isRejected())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @elseif($verification->isExpired())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        Expired
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">{{ $verification->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($verification->isRejected() && $verification->rejection_reason)
                            <p class="text-sm text-red-600 mt-2">
                                <strong>Reason:</strong> {{ $verification->rejection_reason }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
            </div>
        </x-settings.layout>
    </section>
</div>
