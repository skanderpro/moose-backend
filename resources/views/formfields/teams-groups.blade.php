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
        const registry = {!! json_encode($registry) !!};
        const $select2 = $('.teams-groups select').select2();
        $select2.on('change', event => {
            const $select = $(event.target);
            const val = $select.val();
            if (!`${val}`.trim().length) {
                return;
            } else if (registry[val]) {
                $select.val('');
                toastr.error('you have duplicates in your team list');
                $select.trigger('change');
            }

            registry[val] = true;
        });
    });
</script>
