<input type="text" name="is_active" value="1" style="visibility: hidden">
<div class="teams-groups" style="display: flex; gap: 16px;">
    @php
    function renderSelects($key, $tms, $vals)
    {
        for ($i = 0; $i < 16; $i++) {
            @endphp
            <select name="teams_groups[{{ $key }}][]" >
                <option value="">-</option>
                @php
                    foreach($tms as $tm) {
                        @endphp
                            <option value="{{ $tm->id }}" {{  !empty($vals[$i]) && $vals[$i] == $tm->id ? 'selected="selected"' : '' }}>{{ $tm->name }}</option>
                        @php
                    }
                @endphp
            </select>
            @php
        }
    }
    @endphp

    @foreach($groups as $group)
        <div class="panel panel-default" style="display: flex; flex-direction: column; width: 100%;">
            <div class="panel-heading" style="padding: 0 15px;">{{ $group['title'] }}</div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 12px; align-items: stretch;">
                @php
                renderSelects($group['key'], $teams, !empty($values[$group['key']]) ? $values[$group['key']] : []);
                @endphp
            </div>
        </div>
    @endforeach
</div>
<script defer>
    window.addEventListener('load', () => {
        const submit = document.querySelector('.teams-groups').closest('form').querySelector('[type="submit"]');

        function selectChangeHandler(event) {
            $('.select2-selection').removeClass('invalid');
            submit.disabled = false;

            const id = event.target.dataset.select2Id;
            const val = registry[id].select2.val();
            registry[id].val = val;

            let isInvalid = false;

            Object.values(registry).forEach((regItem) => {
                Object.values(registry).forEach((innerRegItem) => {
                    if (innerRegItem.val === regItem.val && innerRegItem.id !== regItem.id && innerRegItem.val.length) {
                        isInvalid = true;
                        $(regItem.select.nextElementSibling).find('.select2-selection').addClass('invalid');
                        $(innerRegItem.select.nextElementSibling).find('.select2-selection').addClass('invalid');
                    }
                });
            });

            if (isInvalid) {
                submit.disabled = true;
                toastr.error('you have duplicates in your team list');
            }
        }

        const registry = Array.from(document.querySelectorAll('.teams-groups select')).reduce((acc, select) => {
            const $select = $(select);
            $select.on('change', selectChangeHandler);
            const regItem = {
                select,
                select2: $select.select2(),
                val: $select.val(),
                id: select.dataset.select2Id,
            };

            acc[regItem.id] = regItem;

            return acc;
        }, {});
    });
</script>

<style>
    .invalid {
        border-color: red !important;
    }
</style>
