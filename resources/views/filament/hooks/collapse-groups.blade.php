<script>
    function collapseFilamentTableGroups() {
        // Берём только таблицы Filament (не sidebar)
        const tables = document.querySelectorAll('.fi-ta');

        tables.forEach((table) => {
            // Внутри таблицы ищем раскрытые кнопки (это именно группы / раскрытия строк)
            const expanded = table.querySelectorAll('button[aria-expanded="true"]');
            expanded.forEach((btn) => btn.click());
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(collapseFilamentTableGroups, 100);
    });

    document.addEventListener('livewire:navigated', () => {
        setTimeout(collapseFilamentTableGroups, 100);
    });
</script>
