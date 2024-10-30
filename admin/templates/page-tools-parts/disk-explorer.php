<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="disk-explorer-container" v-show="current_view === 'disk-explorer'">
            <div class="disk-explorer-loader" v-show="!disk_explorer.loaded">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="file-list-container">
                <nav>
                    <div :class="disk_explorer.parent_folder == disk_explorer.parent_wp_root ? 'inactive' : ''" @click="$root.get_folder_data(disk_explorer.parent_folder)">
                        <i class="bi bi-chevron-left"></i>
                    </div>
                    <div class="home" @click="$root.get_folder_data(disk_explorer.wp_root)">
                        <i class="bi bi-house"></i>
                    </div>
                    <div class="current-folder">
                        <img :src="$root.get_file_icon(
                            {
                                name: disk_explorer.name,
                                is_dir: true
                            }
                        )" alt="icon">
                        <span>
                            {{disk_explorer.name}}
                        </span>
                    </div>
                </nav>
                <ul class="file-list">
                    <li v-for="file in disk_explorer.files" @mouseover="$root.set_focused_file(file)" @mouseleave="$root.set_focused_file('')" :class="{selected: file.path === disk_explorer.selected_file.path, focused: file.path === disk_explorer.focused_file.path}" @click="$root.set_selected_file(file)" @dblclick="$root.get_folder_data( file.path, file.is_dir )">
                        <img :src="$root.get_file_icon(file)" alt="icon">
                        <div class="name">
                            {{ file.name }}
                        </div>
                        <div class="size">
                            {{ $root.bytes_to_readable(file.size) }}
                        </div>
                        <div v-if="file.is_dir" @click="$root.get_folder_data( file.path )" class="folder-actions">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="bubble-chart-container">
                <div id="bubble-chart"></div>
                
                <div class="file-hover-informations-container" v-show="disk_explorer.focused_file || disk_explorer.selected_file" >
                    <div v-if="disk_explorer.focused_file && disk_explorer.focused_file.path !== disk_explorer.selected_file.path">
                        <div class="file-name-container">
                            <img :src="$root.get_file_icon(disk_explorer.focused_file)" alt="icon">
                            <span>
                                {{ disk_explorer.focused_file.name }}
                            </span>
                        </div>
                        <div class="file-action-container"></div>
                        <div class="file-size-container">
                            {{ $root.bytes_to_readable(disk_explorer.focused_file.size) }}
                        </div>
                    </div>
                    <div v-else-if="disk_explorer.selected_file">
                        <div class="file-name-container">
                            <img :src="$root.get_file_icon(disk_explorer.selected_file)" alt="icon">
                            <span>
                                {{ disk_explorer.selected_file.name }}
                            </span>
                        </div>
                        <div class="file-action-container" @click="$root.delete_file(disk_explorer.selected_file)">
                            <i class="bi bi-trash"></i>
                        </div>
                        <div class="file-size-container">
                            {{ $root.bytes_to_readable(disk_explorer.selected_file.size) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>