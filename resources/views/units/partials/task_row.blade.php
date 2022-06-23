            <tr>
                <td>
                    <div class="field has-addons">
                        <div class="control">
                            <button class="button is-static">{{ $task->id }}</button>
                        </div>
                        <div class="control is-expanded">
                            <input class="input is-expeanded" type="text" name="description[{{ $task->id }}]" value="{{ $task->description }}" placeholder="New task...">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <div class="control">
                          @foreach ($peopleTypes->chunk(2) as $chunkedTypes)
                            @foreach ($chunkedTypes as $peopleType)
                                <label class="checkbox">
                                    <input type="checkbox" name="applies_to[{{ $task->id }}][]" value="{{ $peopleType->id }}" @checked($task->peopleTypes->contains($peopleType))>
                                    {{ $peopleType->name }}
                                </label>
                            @endforeach
                            <br />
                          @endforeach
                        </div>
                      </div>
                </td>
                <td>
                    <div class="field">
                        <div class="control">
                        <div class="select">
                            <select name="is_optional[{{ $task->id }}]">
                            <option value="0" @if ($task->isntOptional()) selected @endif>No</option>
                            <option value="1" @if ($task->isOptional()) selected @endif>Yes</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <div class="control">
                        <div class="select">
                            <select name="is_active[{{ $task->id }}]">
                            <option value="0" @if ($task->isntActive()) selected @endif>No</option>
                            <option value="1" @if ($task->isActive()) selected @endif>Yes</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <div class="control">
                        <div class="select">
                            <select name="is_onboarding[{{ $task->id }}]">
                                <option value="1" @if ($task->isOnboarding()) selected @endif>Onboarding</option>
                                <option value="0" @if ($task->isLeaving()) selected @endif>Departing</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </td>
            </tr>
