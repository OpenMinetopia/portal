{{-- Permissions grouped by Spelers/Beheer; $role is null on create. --}}
@php($checked = old('permissions', $role?->permissions->pluck('id')->all() ?? []))

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Permissies</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Een permissie werkt alleen als de bijbehorende feature aanstaat bij Instellingen.</p>
    </div>
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach(\App\Support\Permissions::GROUPS as $group => $label)
            <div class="px-4 py-5 sm:p-6">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</h4>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($permissions->get($group, []) as $permission)
                        @php($feature = \App\Support\Permissions::feature($permission->slug))
                        @php($canGrant = in_array($permission->slug, $grantable))
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}"
                                       value="{{ $permission->id }}"
                                       @checked(in_array($permission->id, $checked))
                                       @disabled(!$canGrant)
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3">
                                <label for="permission_{{ $permission->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $permission->name }}
                                </label>
                                @if($feature && !\App\Support\Features::enabled($feature))
                                    <span class="ml-1 inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                        {{ \App\Support\Features::name($feature) }} staat uit
                                    </span>
                                @endif
                                @if($permission->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@error('permissions.*')
    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
@enderror
