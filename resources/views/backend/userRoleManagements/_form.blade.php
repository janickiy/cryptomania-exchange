<input type="hidden" name="base_key" value="{{ base_key() }}">

<section class="role-details-section">
    <div class="role-section-header">
        <h4 class="role-section-title">{{ __('Role Details') }}</h4>
    </div>
    <div class="role-details-grid">
        <div class="role-field">
            <label for="role-name" class="form-label required">{{ __('Role Name') }}</label>
            {{ Form::text(fake_field('role_name'), old('role_name', isset($userRoleManagement) ? $userRoleManagement->role_name : null), [
                'class' => 'form-control',
                'id' => 'role-name',
                'data-cval-name' => 'The role name field',
                'data-cval-rules' => 'required|escapeInput',
            ]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('role_name') }}">{{ $errors->first('role_name') }}</span>
        </div>
    </div>
</section>

@if($errors->has('roles'))
    <div class="alert alert-danger py-2">
        {{ $errors->first('roles') }}
    </div>
@endif

<div class="role-permissions">
    <div class="role-permissions-header">
        <h4 class="mb-0">{{ __('Route Permissions') }}</h4>
    </div>

    <?php $ModuleClasses = []; ?>

    @foreach($routes as $name => $routeGroups)
        <section class="role-permission-group">
            <div class="role-permission-group-header">
                <div class="form-check role-check">
                    {{ Form::checkbox('module', 1, false, [
                        'class' => "form-check-input flat-red module module_$name",
                        'id' => "role-$name",
                        'data-id' => "$name",
                    ]) }}
                    <label class="form-check-label disable-text-select" for="role-{{ $name }}">
                        {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $name)) }}
                    </label>
                </div>
            </div>

            <div class="role-permission-group-body">
                <?php $allSubModules = true; ?>

                @foreach($routeGroups as $groupName => $permissionLists)
                    <div class="role-subgroup">
                        <div class="role-subgroup-title">
                            <div class="form-check role-check">
                                {{ Form::checkbox('task', 1, false, [
                                    'class' => "form-check-input sub-module flat-red task module_action_$name module_action_{$name}_{$groupName}",
                                    'id' => "task-$name-$groupName",
                                    'data-id' => "{$name}_$groupName",
                                ]) }}
                                <label class="form-check-label disable-text-select" for="task-{{ $name }}-{{ $groupName }}">
                                    {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $groupName)) }}
                                </label>
                            </div>
                        </div>

                        <div class="role-permission-grid">
                            <?php $allItems = true; ?>

                            @foreach($permissionLists as $permissionName => $routeList)
                                <div class="role-permission-item">
                                    <div class="form-check role-check">
                                        {{ Form::checkbox("roles[$name][$groupName][]", $permissionName, isset($userRoleManagement->route_group[$name][$groupName]) ? (in_array($permissionName, $userRoleManagement->route_group[$name][$groupName]) ? true : false) : false, [
                                            'class' => "form-check-input route-item flat-red module_action_$name task_action_{$name}_$groupName",
                                            'id' => "list-$name-$groupName-$permissionName",
                                        ]) }}
                                        <label class="form-check-label disable-text-select" for="list-{{ $name }}-{{ $groupName }}-{{ $permissionName }}">
                                            {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $permissionName)) }}
                                        </label>
                                    </div>
                                </div>

                                <?php
                                    if (!isset($userRoleManagement->route_group[$name][$groupName]) || !in_array($permissionName, $userRoleManagement->route_group[$name][$groupName])) {
                                        $allSubModules = false;
                                        $allItems = false;
                                    }
                                ?>
                            @endforeach

                            <?php
                                if ($allItems) {
                                    $ModuleClasses[] = "module_action_{$name}_{$groupName}";
                                }
                            ?>
                        </div>
                    </div>
                @endforeach

                <?php
                    if ($allSubModules) {
                        $ModuleClasses[] = "module_$name";
                    }
                ?>
            </div>
        </section>
    @endforeach
</div>

<div class="role-form-actions">
    {{ Form::submit($buttonText, ['class' => 'btn btn-primary form-submission-button']) }}
</div>

@section('extraScript')
    <script>
        (function($){
            var selecteModules = {!! json_encode($ModuleClasses) !!};
            for(var i=0; i < selecteModules.length; i++){
                $('.' + selecteModules[i]).prop('checked', true);
            }
        }(jQuery))

    </script>
@endsection
