## Documentation

### API Reference

- **IndexationViewer.getIndexationStatuses**
  - Parameters: (idSite = '', period = 'day', date = 'today', 'segment' = '', status = '', page = 1, limitPerPage = 15)
  - Response is an array of objects containing:
    - `id_archive` — (int) Database ID of the archive row ;
    - `name` — (string) Value of `done*`, where the suffix can contain a specific plugin name and/or segment hash ;
    - `id_site` — (int) ID of the website ;
    - `period` — (string) Period of the archive. Can be any of: day, week, month, year or range ;
    - `period_begin_date` — (string) Date of the beginning of the period (format: `YYYY-MM-DD`) ;
    - `period_end_date` — (string) Date of the end of the period (format: `YYYY-MM-DD`) ;
    - `ts_archived` — (string) Timestamp of the archive process (format: `Y-m-d H:i:s`) ;
    - `value` — (string) Translation of the archive row status (`OK`, `Error` or `Invalidated`).

### Useful links

- https://developer.matomo.org/guides/archiving
- https://developer.matomo.org/guides/archive-data
- https://developer.matomo.org/guides/segments 