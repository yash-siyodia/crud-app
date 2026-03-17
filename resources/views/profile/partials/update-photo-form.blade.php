<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('After updating your profile photo old photo automaticlly deleted.') }}
        </p>
    </header>

    <div class="mt-6">
       
        <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            @if(auth()->user()->profile_photo)
                <img 
                    src="{{ auth()->user()->profilePhotoUrl() }}" 
                    class="w-20 h-20 rounded-full mb-2"
                >
            @endif
            <input 
            type="file" 
            name="profile_photo"
            accept="image/*"
            onchange="previewImage(event)"
            >

            <img id="preview" class="w-20 h-20 rounded-full mt-2" style="display:none;">

            <x-primary-button>
                Save
            </x-primary-button>
        </form>
        
        @if (session('status') === 'photo-updated')
            <p class="text-sm text-green-600 mt-2">Photo updated successfully.</p>
        @endif

        
    </div>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.style.display = 'block';
        }
    </script>
</section>