            <tr>
                <td>
                    <div class="field">
                        <div class="control">
                        <input class="input" type="text" name="description[{{ $task->id }}]" value="{{ $task->description }}" placeholder="New task...">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <div class="control">
                          <label class="checkbox">
                            <input type="checkbox" name="applies_to[{{ $task->id }}][]" value="1">
                            Academics
                          </label>
                          <label class="checkbox">
                            <input type="checkbox" name="applies_to[{{ $task->id }}][]" value="2">
                            Phds
                          </label>
                          <br />
                          <label class="checkbox">
                            <input type="checkbox" name="applies_to[{{ $task->id }}][]" value="3">
                            PDRAs
                          </label>
                          <label class="checkbox">
                            <input type="checkbox" name="applies_to[{{ $task->id }}][]" value="4">
                            MPAs
                          </label>
                          <br />
                          <label class="checkbox">
                            <input type="checkbox" name="applies_to[{{ $task->id }}][]" value="5">
                            Technical
                          </label>
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
